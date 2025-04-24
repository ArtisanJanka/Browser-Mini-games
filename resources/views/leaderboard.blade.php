<x-app-layout>
    <header class="flex justify-center mb-6 text-2xl font-bold text-cyan-300">
        Leaderboard
    </header>

    <div class="max-w-5xl mx-auto space-y-10 px-4">

    @foreach ($groupedScores as $difficulty => $entries)
    <h2 class="text-xl text-cyan-400 mb-4">{{ ucfirst($difficulty) }} Mode</h2>

    <table class="w-full bg-gray-800 rounded-lg shadow mb-6 text-white">
        <thead class="bg-gray-700 text-gray-300">
            <tr class="text-center">
                <th class="px-4 py-2">Rank</th>
                <th class="px-4 py-2">Player</th>
                <th class="px-4 py-2">WPM</th>
                <th class="px-4 py-2">Accuracy</th>
                <th class="px-4 py-2">Time</th>
                <th class="px-4 py-2">Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $index => $entry)
                <tr class="border-t border-gray-700 text-center">
                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                    <td class="px-4 py-2">{{ $entry->user->name ?? 'Anonymous' }}</td>
                    <td class="px-4 py-2">{{ $entry->WPM }}</td>
                    <td class="px-4 py-2">{{ $entry->accuracy }}%</td>
                    <td class="px-4 py-2">{{ number_format($entry->completion_time / 1000, 2) }}s</td>
                    <td class="px-4 py-2">{{ $entry->score }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination Links for Entire Sorted and Grouped Data --}}
    <div class="pagination text-center">
        {{ $scoresPaginated->links() }}  {{-- Pagination links for the entire leaderboard --}}
    </div>
@endforeach




    </div>
</x-app-layout>
