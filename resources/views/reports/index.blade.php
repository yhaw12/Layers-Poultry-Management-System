@extend('layouts.app')

@section('content')
    <div class="text-2xl font-semibold mb-4">Farm Reports</div>
    <div class="container mx-auto">
        <!-- Tabs -->
        <div class="mb-4 border-b">
            <nav class="flex space-x-4">
                <a href="{{ route('reports.index', ['type' => 'type' => 'daily']) }}"
                   class="px-3 py-2 text-sm font-medium {{ $reportType === 'daily' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-800 dark:text-gray-600 hover:text-blue-600' }}">Daily"

                </a>
                <a href="{{ route('reports.index', ['type' => 'weekly']) }}"
                   class="px-3 py-2 text-sm font-medium {{ $reportType === 'weekly' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600' }}">Weekly"

                </a>
                <a href="{{ route('reports.index', ['type' => 'monthly']) }}"
                   class="px-3 py-2 text-sm font-medium {{ $reportType'=='monthly' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600' }}">Monthly"

                </a>
                <a href="{{ route('reports.index', ['type' => 'custom']) }}"
                   class="px-3 py-2 text-sm font-medium {{ $reportType === 'custom' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600' }}">Custom"

                </a>
                <a href="{{ route('reports.index', ['type' => 'profitability']) }}"
                   class="px-3 py-2 text-sm font-medium {{ $reportType === 'profitability' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600 dark:text-gray-400 hover:text-blue-600' }}">Profitability"

                </a>
            </nav>
        </div>

        <!-- Daily Report -->
        @if ($reportType === 'daily')
            <h3 class="text-xl mt-4 mb-2">Daily Egg Report</h3>
            @if (empty($data['daily']) || $data['daily']->isEmpty())
                <p>No egg data found for the last 7 days.</p>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border px-4 py-2 text-left">Date</th>
                            <th class="border px-4 py-2 text-left">Total Eggs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['daily'] as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="border px-4 py-2">{{ $row->date }}</td>
                                <td class="border px-4 py-2">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif

        <!-- Weekly Report -->
        @if ($reportType === 'weekly')
            <h3 class="text-xl mt-4 mb-2">Weekly Egg Report</h3>
            @if (empty($data['weekly']) || $data['weekly']->isEmpty())
                <p>No egg data found for the last 8 weeks.</p>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border px-4 py-2 text-left">Year</th>
                            <th class="border px-4 py-2 text-left">Week</th>
                            <th class="border px-4 py-2 text-left">Total Eggs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['weekly'] as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="border px-4 py-2">{{ $row->year }}</td>
                                <td class="border px-4 py-2">{{ $row->week }}</td>
                                <td class="border px-4 py-2">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif

        <!-- Monthly Report -->
        @if ($reportType === 'monthly')
            <h3 class="text-xl mt-4 mb-2">Monthly Egg Report</h3>
            @if (empty($data['monthly']) || $data['monthly']->isEmpty())
                <p>No egg data found for the last 6 months.</p>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border px-4 py-2 text-left">Year</th>
                            <th class="border px-4 py-2 text-left">Month</th>
                            <th class="border px-4 py-2 text-left">Total Eggs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['monthly'] as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="border px-4 py-2">{{ $row->year }}</td>
                                <td class="border px-4 py-2">{{ $row->month }}</td>
                                <td class="border px-4 py-2">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif

        <!-- Custom Report Form -->
        @if ($reportType === 'custom')
            <h3 class="text-xl mt-4 mb-2">Custom Report</h3>
            <form method="POST" action="{{ route('reports.index') }}">
                @csrf
                <input type="hidden" name="type" value="custom">
                <div class="mb-4">
                    <label class="block text-sm font-medium">Start Date</label>
                    <input type="date" name="start_date" required class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700">
                    @error('start_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">End Date</label>
                    <input type="date" name="end_date" required class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700">
                    @error('end_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Metrics</label>
                    <label><input type="checkbox" name="metrics[]" value="eggs"> Eggs</label>
                    <label><input type="checkbox" name="metrics[]" value="sales"> Sales</label>
                    <label><input type="checkbox" name="metrics[]" value="expenses"> Expenses</label>
                    @error('metrics')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Format</label>
                    <select name="format" required class="mt-1 block w-full border rounded-md p-2 dark:bg-gray-700">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Generate Report</button>
            </form>

            <!-- Display Custom Report Results (if POSTed) -->
            @if (!empty($data['eggs']) || !empty($data['sales']) || !empty($data['expenses']))
                <h3 class="text-xl mt-6 mb-2">Custom Report Results</h3>
                @if (!empty($data['eggs']))
                    <h4 class="text-lg mt-4 mb-2">Eggs</h4>
                    <table class="w-full border-collapse mb-4">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="border px-4 py-2 text-left">Date Laid</th>
                                <th class="border px-4 py-2 text-left">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['eggs'] as $egg)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="border px-4 py-2">{{ $egg->date_laid }}</td>
                                    <td class="border px-4 py-2">{{ $egg->quantity ?? 1 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if (!empty($data['sales']))
                    <h4 class="text-lg mt-4 mb-2">Sales</h4>
                    <table class="w-full border-collapse mb-4">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="border px-4 py-2 text-left">Date</th>
                                <th class="border px-4 py-2 text-left">Customer</th>
                                <th class="border px-4 py-2 text-left">Item</th>
                                <th class="border px-4 py-2 text-left">Quantity</th>
                                <th class="border px-4 py-2 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['sales'] as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="border px-4 py-2">{{ $sale->sale_date }}</td>
                                    <td class="border px-4 py-2">{{ $sale->customer->name ?? 'N/A' }}</td>
                                    <td class="border px-4 py-2">{{ $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A' }}</td>
                                    <td class="border px-4 py-2">{{ $sale->quantity }}</td>
                                    <td class="border px-4 py-2">${{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                @if (!empty($data['expenses']))
                    <h4 class="text-lg mt-4 mb-2">Expenses</h4>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="border px-4 py-2 text-left">Date</th>
                                <th class="border px-4 py-2 text-left">Description</th>
                                <th class="border px-4 py-2 text-left">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['expenses'] as $expense)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="border px-4 py-2">{{ $expense->date }}</td>
                                    <td class="border px-4 py-2">{{ $expense->description ?? 'N/A' }}</td>
                                    <td class="border px-4 py-2">${{ number_format($expense->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        @endif

        <!-- Profitability Report -->
        @if ($reportType === 'profitability')
            <h3 class="text-xl mt-4 mb-2">Profitability Report</h3>
            @if (empty($data['profitability']) || $data['profitability']->isEmpty())
                <p>No profitability data found.</p>
            @else
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border px-4 py-2 text-left">Bird ID</th>
                            <th class="border px-4 py-2 text-left">Breed</th>
                            <th class="border px-4 py-2 text-left">Sales ($)</th>
                            <th class="border px-4 py-2 text-left">Feed Cost ($)</th>
                            <th class="border px-4 py-2 text-left">Expenses ($)</th>
                            <th class="border px-4 py-2 text-left">Profit ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['profitability'] as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="border px-4 py-2">{{ $row['bird_id'] }}</td>
                                <td class="border px-4 py-2">{{ $row['breed'] }}</td>
                                <td class="border px-4 py-2">{{ number_format($row['sales'], 2) }}</td>
                                <td class="border px-4 py-2">{{ number_format($row['feed_cost'], 2) }}</td>
                                <td class="border px-4 py-2">{{ number_format($row['expenses'], 2) }}</td>
                                <td class="border px-4 py-2 {{ $row['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($row['profit'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    </div>
@endsection