@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Latest Surveys</h2>
                
                @if($surveys->isEmpty())
                    <p class="text-gray-500">No surveys have been created yet.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($surveys as $survey)
                            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                                <h3 class="text-xl font-semibold mb-2">{{ $survey->title }}</h3>
                                <p class="text-gray-600 mb-4">{{ Str::limit($survey->description, 100) }}</p>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">
                                        {{ $survey->responses_count }} {{ Str::plural('response', $survey->responses_count) }}
                                    </span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('surveys.show', $survey) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            View
                                        </a>
                                        <a href="{{ route('responses.index', ['survey' => $survey->id]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Responses
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 