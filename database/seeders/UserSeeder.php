<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Admin user
		$admin = User::firstOrCreate(
			['email' => 'admin@socialtoast.com'],
			[
				'name' => 'Administrator',
				'password' => Hash::make('password'),
				'email_verified_at' => now(),
				'remember_token' => Str::random(10),
			]
		);
		$admin->assignRole('Admin');

		// Regular user
		$user = User::firstOrCreate(
			['email' => 'user@socialtoast.com'],
			[
				'name' => 'Regular User',
				'password' => Hash::make('password'),
				'email_verified_at' => now(),
				'remember_token' => Str::random(10),
			]
		);
		$user->assignRole('User');
	}
}
