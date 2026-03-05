<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vice Principal Dashboard') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #8b5cf6, #a78bfa); color: white;">
                    <h3 class="text-2xl font-bold">{{ __('Welcome, VP :name!', ['name' => auth()->user()->name]) }}</h3>
                    <p class="mt-2">{{ __('Oversee academic operations and review reports') }}</p>
                </div>

                <div class="pt-6 px-6 mb-6 flex gap-4">
                    <a href="{{ route('dashboard.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                        <i class="bi bi-download mr-2"></i>{{ __('Export Data') }}
                    </a>
                    <a href="{{ route('dashboard.template') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                        <i class="bi bi-file-earmark-spreadsheet mr-2"></i>{{ __('Download Template') }}
                    </a>
                    <button onclick="document.getElementById('import-file').click()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                        <i class="bi bi-upload mr-2"></i>{{ __('Import Data') }}
                    </button>
                    <form id="import-form" method="POST" action="{{ route('dashboard.import') }}" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" name="file" id="import-file" accept=".xlsx,.xls,.csv" onchange="document.getElementById('import-form').submit()">
                    </form>
                </div>

                <div class="px-6 pb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Activity Reports by Weekday') }}</h4>
                            <form method="GET" action="{{ route('dashboard') }}">
                                <select name="report_range" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="week" {{ $reportRange == 'week' ? 'selected' : '' }}>{{ __('This Week') }}</option>
                                    <option value="month" {{ $reportRange == 'month' ? 'selected' : '' }}>{{ __('This Month') }}</option>
                                </select>
                            </form>
                        </div>
                        <canvas id="reportChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-full">
                                    <i class="bi bi-buildings text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Class Overview') }}</h5>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('Monitor all classes') }}</p>
                                    <a href="{{ route('classes.index') }}" class="mt-2 inline-block text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">{{ __('View Classes →') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-full">
                                    <i class="bi bi-person-badge text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Teacher Performance') }}</h5>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('Review teacher evaluations') }}</p>
                                    <a href="{{ route('activity-reports.index') }}" class="mt-2 inline-block text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300">{{ __('View Evaluations →') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-500 rounded-full">
                                    <i class="bi bi-bar-chart text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Attendance Summary') }}</h5>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('School-wide attendance statistics') }}</p>
                                    <a href="{{ route('activity-presences.index') }}" class="mt-2 inline-block text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300">{{ __('View Summary →') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('partials.schedule-table')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('reportChart').getContext('2d');
            const reportStats = @json($reportStats);
            const labels = reportStats.map(item => item.weekday);
            const data = reportStats.map(item => item.count);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Reports',
                        data: data,
                        backgroundColor: 'rgba(139, 92, 246, 0.5)',
                        borderColor: 'rgba(139, 92, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</x-app-layout>