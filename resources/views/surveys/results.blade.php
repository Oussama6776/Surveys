<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Survey Results: ') }} {{ $survey->title }}
            </h2>
            <a href="{{ route('surveys.show', $survey) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Back to Survey') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('Survey Statistics') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('Total Questions') }}</p>
                                <p class="text-2xl font-semibold">{{ $survey->questions->count() }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('Total Responses') }}</p>
                                <p class="text-2xl font-semibold">{{ $survey->responses->count() }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">{{ __('Completion Rate') }}</p>
                                <p class="text-2xl font-semibold">
                                    {{ number_format(($survey->responses->count() / max($survey->questions->count(), 1)) * 100, 1) }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        @foreach($survey->questions as $question)
                            <div class="border rounded-lg p-4">
                                <h4 class="text-lg font-semibold mb-4">{{ $question->question_text }}</h4>
                                
                                @if($question->questionType->slug === 'multiple_choice')
                                    <div class="space-y-2">
                                        @foreach($question->responseOptions as $option)
                                            @php
                                                $optionCount = $question->responses->where('answer', $option->option_text)->count();
                                                $percentage = $question->responses->count() > 0 
                                                    ? ($optionCount / $question->responses->count()) * 100 
                                                    : 0;
                                            @endphp
                                            <div>
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-sm font-medium text-gray-700">{{ $option->option_text }}</span>
                                                    <span class="text-sm font-medium text-gray-700">{{ number_format($percentage, 1) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->questionType->slug === 'rating')
                                    @php
                                        $ratings = $question->responses->pluck('answer')->map(function($value) {
                                            return (int)$value;
                                        });
                                        $average = $ratings->avg();
                                        $distribution = $ratings->countBy();
                                    @endphp
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">{{ __('Average Rating') }}</span>
                                            <span class="text-lg font-semibold">{{ number_format($average, 1) }}/5</span>
                                        </div>
                                        @for($i = 5; $i >= 1; $i--)
                                            @php
                                                $count = $distribution->get($i, 0);
                                                $percentage = $question->responses->count() > 0 
                                                    ? ($count / $question->responses->count()) * 100 
                                                    : 0;
                                            @endphp
                                            <div>
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-sm font-medium text-gray-700">{{ $i }} stars</span>
                                                    <span class="text-sm font-medium text-gray-700">{{ number_format($percentage, 1) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                @elseif($question->questionType->slug === 'text')
                                    <div class="space-y-4">
                                        <h5 class="text-sm font-medium text-gray-700">{{ __('Recent Responses') }}</h5>
                                        <div class="space-y-2">
                                            @foreach($question->responses->take(5) as $response)
                                                <div class="bg-gray-50 p-3 rounded-lg">
                                                    <p class="text-sm text-gray-700">{{ $response->answer }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ __('By') }} {{ $response->user->name ?? __('Anonymous') }}
                                                        {{ __('on') }} {{ $response->created_at->format('Y-m-d H:i') }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 