<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\Question;
use App\Models\Option;
use App\Models\Response;
use App\Models\ResponseDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        $survey = Survey::create([
            'user_id' => $user->id,
            'title' => 'Sondage Démo',
            'description' => 'Exemple de sondage avec questions variées.',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(7),
        ]);

        $q1 = Question::create(['survey_id'=>$survey->id,'type'=>'short_text','label'=>'Votre prénom ?','required'=>true,'position'=>1]);
        $q2 = Question::create(['survey_id'=>$survey->id,'type'=>'long_text','label'=>'Votre avis sur notre service ?','required'=>false,'position'=>2]);
        $q3 = Question::create(['survey_id'=>$survey->id,'type'=>'single_choice','label'=>'Votre satisfaction ?','required'=>true,'position'=>3]);
        $q4 = Question::create(['survey_id'=>$survey->id,'type'=>'multi_choice','label'=>'Fonctionnalités préférées ?','required'=>false,'position'=>4]);

        foreach (['Très satisfait','Satisfait','Neutre','Insatisfait'] as $i=>$lbl) {
            Option::create(['question_id'=>$q3->id,'label'=>$lbl,'position'=>$i+1]);
        }
        foreach (['Rapidité','Simplicité','Design','Statistiques'] as $i=>$lbl) {
            Option::create(['question_id'=>$q4->id,'label'=>$lbl,'position'=>$i+1]);
        }

        $resp = Response::create([
            'survey_id' => $survey->id,
            'participant_token' => $survey->public_token,
            'participant_ip' => '127.0.0.1',
            'submitted_at' => now(),
        ]);

        ResponseDetail::create(['response_id'=>$resp->id,'question_id'=>$q1->id,'value'=>'Alex']);
        ResponseDetail::create(['response_id'=>$resp->id,'question_id'=>$q2->id,'value'=>'Très bon dans l\'ensemble.']);
    }
}

