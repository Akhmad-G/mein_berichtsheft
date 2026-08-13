<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
  'name', 'email', 'password',
  'vorname', 'nachname', 'ausbildungsberuf',
  'ausbildungsbetrieb', 'ausbildungsbeginn',
])]

// ===
//protected $fillable = [
//  'name', 'email', 'password',
//  'vorname', 'nachname', 'ausbildungsberuf',
//  'ausbildungsbetrieb', 'ausbildungsbeginn',
//];

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ausbildungsbeginn' => 'date',
            'ausbildung_info_completed_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
  
    protected static function booted(): void
    {
      static::saving(function (User $user) {
        if ($user->vorname || $user->nachname) {
          $user->name = trim($user->vorname.' '.$user->nachname);
        }
      });
    }
}
