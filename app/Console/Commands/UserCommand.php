<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user to the system';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Creating a new user...');
        $this->newLine();

        // Get user input
        $name = $this->ask('What is the user\'s name?');
        $email = $this->ask('What is the user\'s email?');
        $password = $this->secret('What is the user\'s password?');
        $confirmPassword = $this->secret('Confirm password');

        // Validate password confirmation
        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match!');

            return;
        }

        // Validate email uniqueness
        if (User::where('email', $email)->exists()) {
            $this->error("A user with email '{$email}' already exists!");

            return;
        }

        // Create the user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $this->newLine();
            $this->info('✓ User created successfully!');
            $this->table(
                ['ID', 'Name', 'Email', 'Created At'],
                [[$user->id, $user->name, $user->email, $user->created_at->format('Y-m-d H:i:s')]]
            );
        } catch (Exception $e) {
            $this->error('Failed to create user: '.$e->getMessage());
        }
    }
}
