@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $survey->title }}</h1>
            @if($survey->description)
                <p class="mt-1 text-sm text-gray-500">{{ $survey->description }}</p>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('responses.store', $survey) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                @foreach($survey->questions as $question)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $question->question_text }}
                            @if($question->is_required)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>

                        @switch($question->type->name)
                            @case('Text')
                                <input type="text" 
                                       name="responses[{{ $question->id }}][answer]" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       @if($question->is_required) required @endif>
                                @break

                            @case('Textarea')
                                <textarea name="responses[{{ $question->id }}][answer]" 
                                          rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                          @if($question->is_required) required @endif></textarea>
                                @break

                            @case('Multiple Choice')
                                @foreach($question->options as $option)
                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" 
                                                   name="responses[{{ $question->id }}][answer]" 
                                                   value="{{ $option->id }}"
                                                   class="form-radio h-4 w-4 text-indigo-600"
                                                   @if($question->is_required) required @endif>
                                            <span class="ml-2">{{ $option->option_text }}</span>
                                        </label>
                                    </div>
                                @endforeach
                                @break

                            @case('Checkbox')
                                @foreach($question->options as $option)
                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" 
                                                   name="responses[{{ $question->id }}][answer][]" 
                                                   value="{{ $option->id }}"
                                                   class="form-checkbox h-4 w-4 text-indigo-600">
                                            <span class="ml-2">{{ $option->option_text }}</span>
                                        </label>
                                    </div>
                                @endforeach
                                @break
                        @endswitch

                        <input type="hidden" name="responses[{{ $question->id }}][question_id]" value="{{ $question->id }}">

                        @error("responses.{$question->id}.answer")
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="mt-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Submit Survey
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 