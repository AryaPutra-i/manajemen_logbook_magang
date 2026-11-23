<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\UniqueEncryptedUserIdentifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class EncryptedUserIdentifierValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_nip_nik_and_nim_are_rejected(): void
    {
        $identifiers = [
            'nip' => ['value' => '123456789012345678', 'message' => 'NIP sudah terdaftar'],
            'nik' => ['value' => '1234567890123456', 'message' => 'NIK sudah terdaftar'],
            'nim' => ['value' => 'NIM-123456', 'message' => 'NIM sudah terdaftar'],
        ];

        foreach ($identifiers as $column => $identifier) {
            User::create([
                'username' => 'user.'.$column,
                'password' => 'password',
                'role' => 'dokter_magang',
                'nama' => 'Test User',
                $column => $identifier['value'],
            ]);

            $validator = Validator::make(
                [$column => $identifier['value']],
                [$column => [new UniqueEncryptedUserIdentifier($column)]]
            );

            $this->assertSame($identifier['message'], $validator->errors()->first($column));
        }
    }

    public function test_user_can_keep_their_own_identifier_during_an_update(): void
    {
        $user = User::create([
            'username' => 'first.user',
            'password' => 'password',
            'role' => 'dokter_magang',
            'nama' => 'First User',
            'nik' => '1234567890123456',
        ]);

        $validator = Validator::make(
            ['nik' => '1234567890123456'],
            ['nik' => [new UniqueEncryptedUserIdentifier('nik', $user->id)]]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_update_cannot_use_another_users_identifier(): void
    {
        User::create([
            'username' => 'first.user',
            'password' => 'password',
            'role' => 'dokter_magang',
            'nama' => 'First User',
            'nik' => '1234567890123456',
        ]);
        $userBeingUpdated = User::create([
            'username' => 'second.user',
            'password' => 'password',
            'role' => 'dokter_magang',
            'nama' => 'Second User',
            'nik' => '6543210987654321',
        ]);

        $validator = Validator::make(
            ['nik' => '1234567890123456'],
            ['nik' => [new UniqueEncryptedUserIdentifier('nik', $userBeingUpdated->id)]]
        );

        $this->assertSame('NIK sudah terdaftar', $validator->errors()->first('nik'));
    }
}
