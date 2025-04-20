<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Take Survey: ') }} {{ $survey->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('responses.store', $survey) }}" class="space-y-6">
                        @csrf

                        @foreach($survey->questions as $question)
                            <div class="border rounded-lg p-4">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $question->question_text }}
                                        @if($question->is_required)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <p class="text-sm text-gray-500">{{ $question->questionType->name }}</p>
                                </div>

                                @if($question->questionType->slug === 'multiple_choice')
                                    <div class="space-y-2">
                                        @foreach($question->responseOptions as $option)
                                            <div class="flex items-center">
                                                <input type="radio" name="responses[{{ $question->id }}][answer]" value="{{ $option->option_text }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ $question->is_required ? 'required' : '' }}>
                                                <label class="ml-3 block text-sm font-medium text-gray-700">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->questionType->slug === 'text')
                                    <textarea name="responses[{{ $question->id }}][answer]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" {{ $question->is_required ? 'required' : '' }}></textarea>
                                @elseif($question->questionType->slug === 'rating')
                                    <div class="flex items-center space-x-2">
                                        <input type="range" name="responses[{{ $question->id }}][answer]" min="1" max="5" class="w-full" {{ $question->is_required ? 'required' : '' }}>
                                        <span class="text-sm text-gray-500" id="rating-value-{{ $question->id }}">3</span>
                                    </div>
                                    <script>
                                        document.querySelector('input[name="responses[{{ $question->id }}][answer]"]').addEventListener('input', function() {
                                            document.getElementById('rating-value-{{ $question->id }}').textContent = this.value;
                                        });
                                    </script>
                                @endif

                                <input type="hidden" name="responses[{{ $question->id }}][question_id]" value="{{ $question->id }}">
                            </div>
                        @endforeach

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Submit Survey') }}</x-primary-button>
                            <a href="{{ route('surveys.show', $survey) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 