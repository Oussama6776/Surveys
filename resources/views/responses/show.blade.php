@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h1 class="text-2xl font-bold text-gray-900">Response Details</h1>
            <p class="mt-1 text-sm text-gray-500">Survey: {{ $survey->title }}</p>
            <p class="mt-1 text-sm text-gray-500">Submitted: {{ $response->submitted_at ? $response->submitted_at->format('Y-m-d H:i:s') : 'Not submitted' }}</p>
        </div>

        <div class="border-t border-gray-200">
            <div class="px-4 py-5 sm:px-6">
                @foreach($response->answers as $answer)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ $answer->question->question_text }}</h3>
                        <div class="mt-2">
                            @if(is_array(json_decode($answer->answer, true)))
                                <ul class="list-disc list-inside">
                                    @foreach(json_decode($answer->answer, true) as $option)
                                        <li class="text-gray-600">{{ $option }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-600">{{ $answer->answer }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-4 py-3 bg-gray-50 sm:px-6">
            <a href="{{ route('responses.index', $survey) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Back to Responses
            </a>
        </div>
    </div>
</div>
@endsection 