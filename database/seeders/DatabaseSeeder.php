<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TextSample;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $samples = [
            [
                'content' => "The quick brown fox jumps over the lazy dog. Every minute of training counts and builds your typing speed over time. Practice daily to improve accuracy and reduce typing errors effectively.",
            ],
            [
                'content' => "Typing is a skill that improves with regular practice and concentration. When you type, try to focus on the words ahead and develop muscle memory. Using all ten fingers increases efficiency and speed. Don't rush—accuracy is just as important as speed.",
            ],
            [
                'content' => "In the digital age, efficient typing has become an essential skill for professionals and students alike. Whether you're writing a report, coding a project, or simply sending an email, your typing speed can significantly influence productivity. Practicing with varying levels of difficulty helps you adapt to different writing contexts. Remember to maintain a good posture, keep your wrists straight, and eyes on the screen. Each mistake is an opportunity to learn and grow. Celebrate small improvements and track your progress to stay motivated.",
            ],
        ];
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);
        foreach ($samples as $sample) {
            TextSample::create([
                'content' => $sample['content'],
                'word_count' => str_word_count($sample['content']),
            ]);
        }
    }
}
