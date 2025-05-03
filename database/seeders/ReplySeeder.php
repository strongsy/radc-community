<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\Reply;
use App\Models\User;
use DateTime;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Random\RandomException;

class ReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws RandomException
     */
    public function run(): void
    {
        // Get all emails
        $emails = Email::all();

        if ($emails->isEmpty()) {
            $this->command->info('No emails found. Please run EmailSeeder first.');

            return;
        }

        // Get or create some users for the replies
        $users = User::take(5)->get();
        if ($users->count() < 5) {
            $usersToCreate = 5 - $users->count();
            $users = $users->merge(User::factory($usersToCreate)->create());
        }

        // Create replies for approximately 60% of emails
        $emailsWithReplies = $emails->random(ceil($emails->count() * 0.6));

        foreach ($emailsWithReplies as $email) {
            // Determine how many replies this email will have (1-4)
            $replyCount = random_int(1, 4);

            for ($i = 0; $i < $replyCount; $i++) {
                // Select a random user to be the replier
                $user = $users->random();

                // Create the reply with proper relationship to the email
                Reply::create([
                    'email_id' => $email->id,
                    'user_id' => $user->id,
                    'subject' => $this->generateReplySubject($email->subject, $i),
                    'message' => $this->generateReplyMessage($email->message, $i),
                    'created_at' => $this->getReplyTimestamp($email->created_at, $i),
                    'updated_at' => $this->getReplyTimestamp($email->created_at, $i),
                ]);
            }
        }
    }

    /**
     * Generate a reply subject based on the original email subject
     */
    private function generateReplySubject(string $originalSubject, int $replyIndex): string
    {
        $prefixes = ['Re:', 'RE:', 'Re: ', 'RE: '];

        // Check if the subject already has a reply prefix
        foreach ($prefixes as $prefix) {
            if (str_starts_with($originalSubject, $prefix)) {
                return $originalSubject; // Keep the existing prefix
            }
        }

        // Add a reply prefix
        return 'Re: '.$originalSubject;
    }

    /**
     * Generate a reply message based on the original email content
     *
     * @throws RandomException
     */
    private function generateReplyMessage(string $originalMessage, int $replyIndex): string
    {
        $faker = Factory::create();

        $replies = [
            "Thank you for your email. I'll look into this matter and get back to you shortly.\n\n",
            "I've received your message and am working on addressing your concerns.\n\n",
            "Thanks for reaching out. Here's what I think we should do next:\n\n",
            "I appreciate your email. Let me provide some information that might help:\n\n",
        ];

        $reply = $replies[$replyIndex % count($replies)].$faker->paragraphs(random_int(1, 3), true);

        // Add quoted original message for some replies
        if (random_int(0, 1)) {
            $reply .= "\n\n----- Original Message -----\n\n";
            $reply .= $originalMessage;
        }

        return $reply;
    }

    /**
     * Generate a timestamp for the reply that's after the original email
     */
    private function getReplyTimestamp(DateTime $emailTimestamp, int $replyIndex): DateTime
    {
        // Each subsequent reply should be after previous replies
        $minHours = 1 + ($replyIndex * 2); // First reply at least 1 hour later, then 3, 5, 7...
        $maxHours = 24 + ($replyIndex * 12); // Maximum delay increases with each reply

        return Factory::create()->dateTimeBetween(
            $emailTimestamp->format('Y-m-d H:i:s')." +$minHours hours",
            $emailTimestamp->format('Y-m-d H:i:s')." +$maxHours hours"
        );
    }
}
