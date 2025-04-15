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
                'content' => "Typing is a valuable skill that improves with daily practice. Start by learning finger placement and keeping your eyes on the screen. Accuracy is more important than speed in the beginning. With time and effort, your muscle memory will develop and typing will become second nature to you.",
            ],            
            [
                'content' => "Good posture is important when typing. Sit upright, place your feet flat on the floor, and keep your hands relaxed. Use all fingers and avoid looking down at the keyboard. With regular practice, your fingers will know where to go without thinking about it too much.",
            ],
            [
                'content' => "To build speed, you must first focus on accuracy. Make sure your fingers rest on the home row keys and return there after every word. Use typing games or lessons to keep practice fun. Over time, your speed will increase naturally as you make fewer mistakes.",
            ],
            [
                'content' => "Typing is more than just hitting keys quickly—it's about precision, posture, and rhythm. To improve, begin each session with a short warm-up, focusing on correct finger placement. Avoid bad habits like watching the keyboard or slouching. Instead, train your brain and fingers to move in sync. Take short breaks during long sessions to avoid fatigue. Tracking your progress with typing tests can be highly motivating. Try to practice daily, even if just for ten minutes. Over time, you’ll notice not only a boost in speed but also a huge jump in your overall typing confidence.",
            ],
            [
                'content' => "When learning to type, the best results come from consistent, focused practice. Choose a quiet space free from distractions. Start slow and ensure each keystroke is correct before speeding up. Don’t get discouraged by mistakes—they’re part of the process. Take time to review common errors and learn from them. Vary your exercises by typing different types of texts such as stories, code, or news articles. This helps you adapt to a wide range of vocabulary and punctuation. Remember, typing is a skill that builds over time. Stay patient, and results will come.",
            ],
            [
                'content' => "Becoming a fast and accurate typist requires more than just practice—it involves technique, discipline, and consistent review. Begin by ensuring your hands are properly positioned on the keyboard, with your fingers resting lightly on the home row keys. Each finger should be responsible for a specific set of keys. Try not to rely on your eyes to find letters; instead, trust your muscle memory. Daily practice sessions are key, even if they're only ten to fifteen minutes long. Mix up your practice with texts of different difficulty levels. Don’t just type words you know—challenge yourself with new ones, and include punctuation and capital letters for full-sentence realism. Use online tools to measure your progress in words per minute and accuracy rate. Identify weak spots and create mini drills to target those specific areas. Remember, improvement comes gradually. Celebrate small wins and stay motivated through the process. Success is typing smarter, not just faster.",
            ],
            [
                'content' => "Typing at a professional level means combining speed, accuracy, and endurance. It’s not just about how fast you can type a sentence—it’s about doing so consistently over time without losing focus or precision. Start by mastering your posture: sit straight with both feet flat on the ground, and position your hands so that your fingers lightly rest on the home row. Your elbows should be close to your body, and your wrists should hover—not rest—above the keyboard. Each finger has designated keys, and training yourself to use the correct finger for each key is essential to avoid forming bad habits. Eye movement is just as important. Resist the urge to look down at your keyboard. Instead, train your eyes to stay on the screen, reading ahead as you type. This allows your fingers to follow instinctively and boosts your overall rhythm. Use guided lessons and daily exercises to strengthen finger coordination and develop reliable muscle memory. Build your stamina by typing longer passages without breaks, and mix in texts with lots of punctuation, numbers, or capitalization to make your training realistic and challenging. Don’t limit yourself to easy or repetitive material—variety builds versatility. Track your words per minute and accuracy with typing tests regularly. But don’t just focus on numbers—review your mistakes carefully and understand why you’re making them. Are they due to specific keys, hand placement, or mental fatigue? Identify patterns and adjust accordingly. Consistency is key, so set a realistic goal to practice every day, even if it’s just for a short period. Take short breaks every 20–30 minutes to avoid hand strain and maintain mental sharpness. Remember, typing is a lifelong skill that benefits from ongoing improvement. With time, patience, and regular effort, you’ll find yourself typing with speed, grace, and confidence. Celebrate every milestone—you’re making progress.",
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
