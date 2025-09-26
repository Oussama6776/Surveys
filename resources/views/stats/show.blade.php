@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Statistiques — {{ $survey->title }}</h1>
<p class="mb-3">Nombre de réponses : <strong>{{ $survey->responses->whereNotNull('submitted_at')->count() }}</strong></p>

<div class="grid md:grid-cols-2 gap-4">
  @foreach ($survey->questions as $q)
    @if (in_array($q->type, ['single_choice','multi_choice']))
      <div class="bg-white border rounded p-3">
        <div class="font-medium mb-2">{{ $q->label }}</div>
        <ul class="text-sm text-gray-700 list-disc list-inside">
          @foreach ($q->options as $o)
            @php
              $count = $survey->responses->flatMap->details->where('question_id', $q->id)->where('option_id', $o->id)->count();
            @endphp
            <li>{{ $o->label }} — {{ $count }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  @endforeach
</div>
@endsection

