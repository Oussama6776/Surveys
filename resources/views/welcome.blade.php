<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Survey Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-xl font-bold text-indigo-600">Survey Tool</span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                        <a href="{{ route('register') }}" class="ml-4 bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Register</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="text-3xl font-bold text-gray-900 mb-6">Welcome to Survey Tool</h1>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-indigo-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-indigo-700 mb-3">Create Surveys</h2>
                                <p class="text-gray-600">Easily create and customize surveys with our intuitive interface. Add different types of questions and set survey parameters.</p>
                            </div>
                            
                            <div class="bg-indigo-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-indigo-700 mb-3">Collect Responses</h2>
                                <p class="text-gray-600">Share your surveys with participants and collect responses in real-time. Track completion rates and response times.</p>
                            </div>
                            
                            <div class="bg-indigo-50 p-6 rounded-lg">
                                <h2 class="text-xl font-semibold text-indigo-700 mb-3">Analyze Results</h2>
                                <p class="text-gray-600">View detailed analytics and export your survey results. Get insights from your collected data.</p>
                            </div>
                        </div>

                        <div class="mt-8 text-center">
                            <p class="text-gray-600 mb-4">Ready to get started?</p>
                            <div class="space-x-4">
                                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Create an Account
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Sign In
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 