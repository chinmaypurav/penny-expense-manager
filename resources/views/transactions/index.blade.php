<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
<div class="py-6 px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Transactions</h1>
            <p class="mt-2 text-lg text-emerald-600 font-semibold">
                Account Name: {{ $account->label }} <br>
                Initial Balance: {{ number_format($currentBalance = $account->initial_balance, 2) }} <br>
                Current Balance: {{ number_format($account->current_balance, 2) }}
            </p>
        </div>
    </div>

    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Sr</th>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Date</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Description
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Amount</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Balance</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Transaction
                            Type
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($transactions as $transaction)
                        <tr class="{{ $transaction['type'] === 'CR' ? 'bg-emerald-50 hover:bg-emerald-100' : 'bg-rose-50 hover:bg-rose-100' }} transition duration-150">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                                {{ $loop->iteration }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                {{$transaction['date'] }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                {{ \Illuminate\Support\Str::limit($transaction['description'], 50) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-600">
                                {{ number_format($a = $transaction['amount'], 2) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-600">
                                {{ number_format(($currentBalance = $transaction['type'] === 'CR' ? $currentBalance + $a : $currentBalance - $a), 2) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-center {{ $transaction['type'] === 'CR' ? 'text-emerald-600' : 'text-rose-600' }} font-medium">
                                {{ $transaction['type'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-sm text-center text-gray-500">
                                No transactions found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
