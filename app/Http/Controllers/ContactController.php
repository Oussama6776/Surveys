<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Contact;
use App\Models\User;
use App\Models\Role;
use App\Models\ContactGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\SurveyInviteMail;
use Illuminate\Support\Facades\Schema;

class ContactController extends Controller
{
    public function index(Survey $survey)
    {
        $this->authorizeSurvey($survey);
        $query = Contact::query();
        if (Schema::hasTable('contact_groups') && Schema::hasColumn('contacts', 'contact_group_id')) {
            $query->whereHas('group', function ($q) use ($survey) {
                $q->where('survey_id', $survey->id)
                  ->when(auth()->check(), fn($qq) => $qq->where('created_by', auth()->id()));
            });
        } else {
            if (Schema::hasColumn('contacts', 'survey_id')) {
                $query->where('survey_id', $survey->id);
            } else {
                $query->whereRaw('1=0');
            }
        }
        $contacts = $query->latest()->paginate(20);
        return view('contacts.index', compact('survey', 'contacts'));
    }

    public function import(Request $request, Survey $survey)
    {
        $this->authorizeSurvey($survey);

        $request->validate([
            'contacts_file' => 'required|file|mimes:csv,txt,xlsx',
            'group_name' => 'nullable|string|max:255',
        ]);

        // Warn if mailer is not smtp
        if (config('mail.default') !== 'smtp') {
            \Log::warning('[Contacts Import] Mailer not set to smtp', [
                'mail.default' => config('mail.default')
            ]);
        }

        // Sécuriser le schéma (création à la volée si manquant)
        $this->ensureContactGroupsSchema();
        $this->ensureContactsAugmentedSchema();
        $this->ensureInviteTokensSchema();

        $file = $request->file('contacts_file');
        $rows = $this->readContactsFile($file->getRealPath(), $file->getClientOriginalExtension());
        if (empty($rows)) {
            return back()->with('error', 'Aucune donnée valide détectée (attendu colonnes: nom, prenom, email).');
        }

        // Créer le groupe (privé au créateur et lié au sondage)
        $groupName = $request->input('group_name') ?: 'Import ' . now()->format('Y-m-d H:i');
        $group = ContactGroup::firstOrCreate([
            'survey_id' => $survey->id,
            'created_by' => auth()->id(),
            'name' => $groupName,
        ]);

        $imported = 0; $sent = 0; $errors = 0; $seenEmails = [];
        foreach ($rows as $data) {
            $email = strtolower(trim($data['email'] ?? ''));
            $nom = trim($data['nom'] ?? '');
            $prenom = trim($data['prenom'] ?? '');
            if (!$email || !$nom || !$prenom) { $errors++; continue; }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors++; continue; }
            if (in_array($email, $seenEmails, true)) { $errors++; continue; }
            $seenEmails[] = $email;

            // Règle d'import souhaitée:
            // - Si le contact (email) existe déjà DANS LE MÊME GROUPE -> on le MET À JOUR
            // - Si le contact existe mais dans UN AUTRE GROUPE -> on CRÉE UNE NOUVELLE ENTRÉE associée à ce nouveau groupe
            // - Sinon -> on CRÉE une nouvelle entrée

            $baseScope = Contact::query()
                ->where('email', $email)
                ->when(Schema::hasColumn('contacts', 'survey_id'), fn($q) => $q->where('survey_id', $survey->id));

            // Restreindre aux groupes créés par l'utilisateur courant (protection d'accès)
            if (Schema::hasTable('contact_groups') && Schema::hasColumn('contacts', 'contact_group_id')) {
                $baseScope->whereHas('group', function ($q) use ($survey) {
                    $q->where('survey_id', $survey->id)
                      ->where('created_by', auth()->id());
                });
            }

            $existingSameGroup = (clone $baseScope)
                ->when(Schema::hasColumn('contacts', 'contact_group_id'), fn($q) => $q->where('contact_group_id', $group->id))
                ->first();

            if ($existingSameGroup) {
                // Mettre à jour les informations du contact dans le même groupe
                $existingSameGroup->nom = $nom;
                $existingSameGroup->prenom = $prenom;
                $existingSameGroup->created_by = auth()->id();
                // Optionnel: régénérer un mot de passe à chaque import
                $plainPassword = Str::random(10);
                $existingSameGroup->password = Hash::make($plainPassword);
                $existingSameGroup->status_envoi = 'en_attente';
                $existingSameGroup->save();
                $contact = $existingSameGroup;
            } else {
                // Vérifier si le contact existe dans un autre groupe (même sondage, même créateur)
                $existsOtherGroup = (clone $baseScope)
                    ->when(Schema::hasColumn('contacts', 'contact_group_id'), fn($q) => $q->where('contact_group_id', '!=', $group->id))
                    ->exists();

                // Dans les deux cas (existe ailleurs ou pas du tout), on crée une NOUVELLE entrée liée à ce groupe
                $contact = new Contact();
                if (Schema::hasColumn('contacts', 'survey_id')) {
                    $contact->survey_id = $survey->id;
                }
                $contact->contact_group_id = $group->id;
                $contact->created_by = auth()->id();
                $contact->nom = $nom;
                $contact->prenom = $prenom;
                $contact->email = $email;
                $contact->status_envoi = 'en_attente';
                $plainPassword = Str::random(10);
                $contact->password = Hash::make($plainPassword);
                $contact->save();
            }

            $imported++;

            // Créer un token d'invitation sécurisé (valide 14 jours)
            // Révoquer d'anciens tokens avant d'en créer un nouveau
            \App\Models\SurveyInviteToken::where('survey_id', $survey->id)->where('contact_id', $contact->id)->delete();
            $plainInviteToken = Str::random(64);
            $inviteTokenHash = hash('sha256', $plainInviteToken);
            $inviteToken = \App\Models\SurveyInviteToken::create([
                'survey_id' => $survey->id,
                'contact_id' => $contact->id,
                'token' => $inviteTokenHash,
                'expires_at' => $survey->end_date, // expiration liée au sondage (si définie)
            ]);

            // Envoyer l'invitation par email (identifiants + lien avec token)
            try {
                Mail::to($email)->send(new \App\Mail\ContactInviteMail($survey, $contact, $plainPassword, $plainInviteToken));
                $contact->status_envoi = 'envoye';
                $contact->date_envoi = now();
                $contact->last_error = null;
                $contact->save();
                $sent++;
            } catch (\Throwable $e) {
                $contact->status_envoi = 'echoue';
                $contact->last_error = $e->getMessage();
                $contact->save();
                \Log::error('[Contacts Import] Email send failed', [
                    'survey_id' => $survey->id,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        return back()->with('success', "Import terminé. Importés: {$imported}, Emails envoyés: {$sent}, Erreurs: {$errors}");
    }

    public function template(Survey $survey)
    {
        $this->authorizeSurvey($survey);

        $filename = 'trame_contacts.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $out = fopen('php://output', 'w');
            // En-têtes obligatoires + option téléphone
            fputcsv($out, ['nom', 'prenom', 'email', 'telephone']);
            // Lignes vides pour faciliter le remplissage
            for ($i = 0; $i < 10; $i++) {
                fputcsv($out, ['', '', '', '']);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(Survey $survey)
    {
        $this->authorizeSurvey($survey);

        $filename = 'contacts_survey_' . $survey->id . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $contacts = Contact::query()
            ->when(Schema::hasTable('contact_groups') && Schema::hasColumn('contacts', 'contact_group_id'), function ($q) use ($survey) {
                $q->whereHas('group', function ($qq) use ($survey) {
                    $qq->where('survey_id', $survey->id)
                       ->when(auth()->check(), fn($qqq) => $qqq->where('created_by', auth()->id()));
                });
            }, function ($q) use ($survey) {
                if (Schema::hasColumn('contacts', 'survey_id')) {
                    $q->where('survey_id', $survey->id);
                } else {
                    $q->whereRaw('1=0');
                }
            })
            ->orderBy('created_at')
            ->get();

        $callback = function () use ($contacts) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['nom', 'prenom', 'email', 'status_envoi', 'date_envoi']);
            foreach ($contacts as $c) {
                fputcsv($out, [
                    $c->nom,
                    $c->prenom,
                    $c->email,
                    $c->status_envoi,
                    optional($c->date_envoi)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sendFromGroup(Request $request, Survey $survey)
    {
        $this->authorizeSurvey($survey);
        if (!Schema::hasTable('contact_groups')) {
            return back()->with('error', 'La table des groupes de contacts est manquante.');
        }
        $data = $request->validate([
            'contact_group_id' => 'required|integer',
        ]);

        $group = ContactGroup::where('id', $data['contact_group_id'])
            ->where('survey_id', $survey->id)
            ->where('created_by', auth()->id())
            ->firstOrFail();

        $contacts = Contact::query()
            ->where('contact_group_id', $group->id)
            ->when(Schema::hasColumn('contacts', 'survey_id'), fn($q) => $q->where('survey_id', $survey->id))
            ->get();

        $sent = 0; $errors = 0;
        foreach ($contacts as $contact) {
            // Toujours générer un nouveau mot de passe pour inclure dans l'email
            $plainPassword = Str::random(10);
            $contact->password = Hash::make($plainPassword);
            $contact->save();

            // Créer un token d'invitation pour cet envoi
            \App\Models\SurveyInviteToken::where('survey_id', $survey->id)->where('contact_id', $contact->id)->delete();
            $plainInviteToken = Str::random(64);
            $inviteTokenHash = hash('sha256', $plainInviteToken);
            $inviteToken = \App\Models\SurveyInviteToken::create([
                'survey_id' => $survey->id,
                'contact_id' => $contact->id,
                'token' => $inviteTokenHash,
                'expires_at' => $survey->end_date, // expiration liée au sondage (si définie)
            ]);

            try {
                Mail::to($contact->email)->send(new \App\Mail\ContactInviteMail($survey, $contact, $plainPassword, $plainInviteToken));
                $contact->status_envoi = 'envoye';
                $contact->date_envoi = now();
                $contact->last_error = null;
                $contact->save();
                $sent++;
            } catch (\Throwable $e) {
                $contact->status_envoi = 'echoue';
                $contact->last_error = $e->getMessage();
                $contact->save();
                $errors++;
            }
        }

        return back()->with('success', "Invitations envoyées. Emails envoyés: {$sent}, Erreurs: {$errors}");
    }

    private function authorizeSurvey(Survey $survey)
    {
        $user = auth()->user();
        if (!$user || !$user->canModifySurvey($survey)) {
            abort(403, 'Accès refusé');
        }
    }

    // Création automatique de contact_groups si absent
    private function ensureContactGroupsSchema(): void
    {
        if (!Schema::hasTable('contact_groups')) {
            Schema::create('contact_groups', function ($table) {
                $table->id();
                $table->foreignId('survey_id')->constrained()->onDelete('cascade');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->string('name');
                $table->timestamps();
                $table->unique(['survey_id', 'created_by', 'name']);
            });
        }
    }

    // Ajoute les colonnes indispensables sur contacts si manquantes
    private function ensureContactsAugmentedSchema(): void
    {
        if (Schema::hasTable('contacts')) {
            if (!Schema::hasColumn('contacts', 'contact_group_id')) {
                Schema::table('contacts', function ($table) {
                    $table->unsignedBigInteger('contact_group_id')->nullable()->after('survey_id');
                });
                Schema::table('contacts', function ($table) {
                    $table->foreign('contact_group_id')->references('id')->on('contact_groups')->nullOnDelete();
                });
            }
            if (!Schema::hasColumn('contacts', 'created_by')) {
                Schema::table('contacts', function ($table) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('contact_group_id');
                });
                Schema::table('contacts', function ($table) {
                    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                });
            }
            if (!Schema::hasColumn('contacts', 'password')) {
                Schema::table('contacts', function ($table) {
                    $table->string('password')->nullable()->after('email');
                });
            }
        }
    }

    private function ensureInviteTokensSchema(): void
    {
        if (!Schema::hasTable('survey_invite_tokens')) {
            Schema::create('survey_invite_tokens', function ($table) {
                $table->id();
                $table->foreignId('survey_id')->constrained()->onDelete('cascade');
                $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
                $table->string('token', 128)->unique();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
            });
        }
    }

    // Lecture CSV/XLSX minimale sans dépendances externes
    private function readContactsFile(string $path, string $extension): array
    {
        $ext = strtolower($extension);
        if ($ext === 'xlsx') {
            return $this->readXlsx($path);
        }
        return $this->readCsv($path);
    }

    private function readCsv(string $path): array
    {
        $h = @fopen($path, 'r');
        if (!$h) return [];
        $header = fgetcsv($h, 0, ',');
        if (!$header) { fclose($h); return []; }
        $header = array_map(fn($v) => strtolower(trim((string)$v)), $header);
        $required = ['nom','prenom','email'];
        foreach ($required as $c) { if (!in_array($c, $header, true)) { fclose($h); return []; } }
        $rows = [];
        while (($row = fgetcsv($h, 0, ',')) !== false) {
            if (count($row) !== count($header)) continue;
            $rows[] = array_combine($header, $row);
        }
        fclose($h);
        return $rows;
    }

    private function readXlsx(string $path): array
    {
        if (!class_exists('ZipArchive')) return [];
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return [];
        $shared = [];
        if (($sxml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $sx = @simplexml_load_string($sxml);
            if ($sx && isset($sx->si)) {
                foreach ($sx->si as $si) {
                    if (isset($si->t)) {
                        $shared[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $txt = '';
                        foreach ($si->r as $r) { $txt .= (string)$r->t; }
                        $shared[] = $txt;
                    } else {
                        $shared[] = '';
                    }
                }
            }
        }
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) { $sheetXml = $zip->getFromName('xl/worksheets/sheet2.xml'); }
        if ($sheetXml === false) { $zip->close(); return []; }
        $sheet = @simplexml_load_string($sheetXml);
        if (!$sheet) { $zip->close(); return []; }
        $colToIndex = function (string $ref): int {
            $col = preg_replace('/\d+/', '', strtoupper($ref));
            $n = 0; for ($i=0; $i<strlen($col); $i++) { $n = $n*26 + (ord($col[$i]) - 64); }
            return $n-1;
        };
        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $t = (string)($c['t'] ?? '');
                $v = (string)($c->v ?? '');
                $idx = $colToIndex((string)$c['r']);
                if ($t === 's') {
                    $val = isset($shared[(int)$v]) ? $shared[(int)$v] : '';
                } elseif ($t === 'inlineStr') {
                    $val = isset($c->is->t) ? (string)$c->is->t : '';
                } else {
                    $val = $v;
                }
                $cells[$idx] = $val;
            }
            if (!empty($cells)) $rows[] = $cells;
        }
        $zip->close();
        if (count($rows) < 1) return [];
        $headerRow = $rows[0];
        $max = max(array_keys($headerRow));
        $header = [];
        for ($i=0; $i<=$max; $i++) { $header[$i] = isset($headerRow[$i]) ? strtolower(trim((string)$headerRow[$i])) : ''; }
        $required = ['nom','prenom','email'];
        foreach ($required as $c) { if (!in_array($c, $header, true)) return []; }
        $out = [];
        for ($r=1; $r<count($rows); $r++) {
            $assoc = [];
            for ($i=0; $i<=$max; $i++) {
                $key = $header[$i] ?? '';
                if ($key === '') continue;
                $assoc[$key] = isset($rows[$r][$i]) ? trim((string)$rows[$r][$i]) : '';
            }
            if (!empty($assoc)) $out[] = $assoc;
        }
        return $out;
    }
}
