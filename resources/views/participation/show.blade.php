@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-4">{{ $survey->title }}</h1>
<p class="text-gray-700 mb-4">{{ $survey->description }}</p>

<form method="POST" action="{{ route('participation.submit', $survey->public_token) }}" class="bg-white border rounded p-4 grid gap-4">
  @csrf
  @foreach ($survey->questions as $q)
    <div>
      <label class="block font-medium mb-1">{{ $q->label }} @if($q->required)<span class="text-red-600">*</span>@endif</label>
      @if ($q->type === 'short_text')
        <input class="w-full border rounded p-2" name="answers[{{ $q->id }}]">
      @elseif ($q->type === 'long_text')
        <textarea class="w-full border rounded p-2" name="answers[{{ $q->id }}]"></textarea>
      @elseif ($q->type === 'single_choice')
        @foreach ($q->options as $o)
          <label class="block"><input type="radio" name="answers[{{ $q->id }}]" value="{{ $o->id }}"> {{ $o->label }}</label>
        @endforeach
      @elseif ($q->type === 'multi_choice')
        @foreach ($q->options as $o)
          <label class="block"><input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $o->id }}"> {{ $o->label }}</label>
        @endforeach
      @endif
    </div>
  @endforeach
  <button class="px-4 py-2 bg-blue-600 text-white rounded">Envoyer</button>
</form>
@endsection

