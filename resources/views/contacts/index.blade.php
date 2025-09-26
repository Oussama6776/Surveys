@extends('layouts.app')

@section('title', 'Contacts - ' . $survey->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Contacts du sondage: {{ $survey->title }}</h1>
            <a href="{{ route('surveys.show', $survey) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour au sondage</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($contacts->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Groupe</th>
                            <th>Status Envoi</th>
                            <th>Date Envoi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $c)
                        <tr>
                            <td>{{ $c->nom }}</td>
                            <td>{{ $c->prenom }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->group?->name ?? '—' }}</td>
                            <td>
                                @php $badge = match($c->status_envoi){'envoye'=>'success','echoue'=>'danger',default=>'secondary'}; @endphp
                                <span class="badge bg-{{ $badge }}">{{ strtoupper($c->status_envoi) }}</span>
                            </td>
                            <td>{{ $c->date_envoi?->format('d/m/Y H:i') ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $contacts->links() }}
            </div>
            @else
            <div class="text-center text-muted py-5">Aucun contact importé pour le moment.</div>
            @endif
        </div>
    </div>
</div>
@endsection
