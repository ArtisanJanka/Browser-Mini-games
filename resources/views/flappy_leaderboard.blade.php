<x-app-layout>
    <header class="flex justify-center mb-6 text-2xl font-bold text-cyan-300">
        Flappy Bird Leaderboard
    </header>

    <div class="max-w-3xl mx-auto space-y-8 px-4">
        <table class="w-full bg-gray-800 rounded-lg shadow text-white">
            <thead class="bg-gray-700 text-gray-300">
                <tr class="text-center">
                    <th class="px-4 py-2">Rank</th>
                    <th class="px-4 py-2">Player</th>
                    <th class="px-4 py-2">Score</th>
                    <th class="px-4 py-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scores as $index => $entry)
                    <tr class="border-t border-gray-700 text-center">
                        <td class="px-4 py-2">{{ ($scores->firstItem() ?? 0) + $index }}</td>
                        <td class="px-4 py-2">{{ $entry->user->name ?? 'Anonymous' }}</td>
                        <td class="px-4 py-2 font-semibold text-yellow-300">{{ $entry->score }}</td>
                        <td class="px-4 py-2 text-sm text-gray-400">{{ $entry->created_at->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination text-center">
            {{ $scores->links() }}
        </div>
    </div>
</x-app-layout>
