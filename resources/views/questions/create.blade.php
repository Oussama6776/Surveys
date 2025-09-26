@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
            <div class="px-6 py-8 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-2xl font-semibold text-gray-800">Ajouter une question</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Rédigez la question puis cliquez sur « Suivant » pour en ajouter une autre, ou « Terminer » pour finaliser.
                </p>
            </div>

            <form action="{{ route('questions.store', $survey) }}" method="POST" x-data="{ questionType: '1', options: ['', ''] }" class="divide-y divide-gray-200">
                @csrf
                
                <div class="px-6 py-8">
                    <div class="space-y-8">
                        <div>
                            <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">Intitulé de la question</label>
                            <div class="mt-1">
                                <input type="text" name="question_text" id="question_text" 
                                       class="block w-full px-4 py-3 border-2 border-black rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                       value="{{ old('question_text') }}" required>
                            </div>
                            @error('question_text')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div>
                                <label for="question_type_id" class="block text-sm font-medium text-gray-700 mb-2">Type de question</label>
                                <select name="question_type_id" id="question_type_id" x-model="questionType"
                                        class="block w-full px-4 py-3 border-2 border-black rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                                    @foreach($questionTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('question_type_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end">
                                <div class="flex items-center">
                                    <input type="hidden" name="is_required" value="0">
                                    <input type="checkbox" name="is_required" id="is_required" value="1" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           {{ old('is_required') ? 'checked' : '' }}>
                                    <label for="is_required" class="ml-2 block text-sm text-gray-700">
                                        Réponse obligatoire
                                    </label>
                                </div>
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
                                        Ajouter une option
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
                    <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                        <a href="{{ route('surveys.show', $survey) }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Annuler
                        </a>
                        <!-- Suivant: enregistre et revient sur l'ajout d'une nouvelle question -->
                        <button type="submit" name="next_action" value="next" class="inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Suivant
                        </button>
                        <!-- Terminer: enregistre et redirige vers les détails du sondage -->
                        <button type="submit" name="next_action" value="finish" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Terminer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 
