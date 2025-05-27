<div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
    <h2 class="text-xl font-semibold">Profitability by Bird</h2>
    <table class="min-w-full">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left">Bird ID</th>
                <th>Breed</th>
                <th>Profit (KES)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profitData as $item)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td>{{ $item['id'] }}</td>
                    <td>{{ $item['breed'] }}</td>
                    <td>{{ number_format($item['profit'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>