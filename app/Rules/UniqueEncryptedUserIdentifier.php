<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueEncryptedUserIdentifier implements ValidationRule
{
    private const COLUMNS = ['nip', 'nik', 'nim'];

    public function __construct(
        private string $column,
        private ?int $ignoreUserId = null,
    ) {
        if (! in_array($this->column, self::COLUMNS, true)) {
            throw new \InvalidArgumentException('Kolom identitas tidak valid.');
        }
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $users = User::query()->whereNotNull($this->column);

        if ($this->ignoreUserId !== null) {
            $users->whereKeyNot($this->ignoreUserId);
        }

        $isDuplicate = $users
            ->get([$this->column])
            ->contains(fn (User $user): bool => $user->getAttribute($this->column) === $value);

        if ($isDuplicate) {
            $fail(match ($this->column) {
                'nip' => 'NIP sudah terdaftar',
                'nik' => 'NIK sudah terdaftar',
                'nim' => 'NIM sudah terdaftar',
            });
        }
    }
}
