@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
            <div class="px-6 py-8 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-2xl font-semibold text-gray-800">Edit Question</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Update your question details
                </p>
            </div>

            <form action="{{ route('questions.update', [$survey, $question]) }}" method="POST" x-data="{ questionType: '{{ $question->question_type_id }}', options: {{ json_encode(old('options', $question->options->pluck('option_text')->toArray()) ?: ['', '']) }} }" class="divide-y divide-gray-200">
                @csrf
                @method('PUT')
                
                <div class="px-6 py-8">
                    <div class="space-y-8">
                        <div>
                            <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>
                            <div class="mt-1">
                                <input type="text" name="question_text" id="question_text" 
                                       class="block w-full px-4 py-3 border-2 border-black rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                       value="{{ old('question_text', $question->question_text) }}" required>
                            </div>
                            @error('question_text')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div>
                                <label for="question_type_id" class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                                <select name="question_type_id" id="question_type_id" x-model="questionType"
                                        class="block w-full px-4 py-3 border-2 border-black rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                                    @foreach($questionTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('question_type_id', $question->question_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('question_type_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="is_required" value="0">
                                    <input type="checkbox" name="is_required" class="sr-only peer" value="1" {{ old('is_required', $question->is_required) ? 'checked' : '' }}>
                                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Required</span>
                                </label>
                            </div>
                        </div>

                        <template x-if="questionType == '2' || questionType == '3'">
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Options</label>
                                    <button type="button" 
                                            @click="options.push('')"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Option
                                    </button>
                                </div>
                                <div class="space-y-4">
                                    <template x-for="(option, index) in options" :key="index">
                                        <div class="flex items-center space-x-4">
                                            <input type="text" 
                                                   :name="'options[]'" 
                                                   x-model="options[index]"
                                                   class="block w-full px-4 py-3 border-2 border-black rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                                   required>
                                            <button type="button" 
                                                    @click="options.splice(index, 1)"
                                                    class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out"
                                                    x-show="options.length > 2">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                @error('options')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </template>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('surveys.show', $survey) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Update Question
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 