<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use function Pest\Laravel\get;

#[Fillable([
  'email', 'password',
  'vorname', 'nachname', 'ausbildungsberuf',
  'ausbildungsbetrieb', 'ausbildungsbeginn',
  'ausbildung_info_completed_at',
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
      static::created(function (User $user) {
        $user->assignGitlabPathIfMissing();
      });
      
      static::updated(function (User $user) {
        $user->assignGitlabPathIfMissing();
      });
    }
  
    public function assignGitlabPathIfMissing(): void
    {
      if ($this->gitlab_path) {
        return;
      }
      
      if (! $this->vorname && ! $this->nachname) {
        return;
      }
      
      $slug = Str::slug($this->fullNameReversed(), '-', 'de');
      $this->gitlab_path = "{$slug}-{$this->id}";
      $this->saveQuietly();
    }
    
    protected function name(): Attribute
    {
      return Attribute::make(
        get: fn () => trim("{$this->vorname} {$this->nachname}"),
      );
    }
  
    public function fullNameReversed(): string
    {
      return trim("{$this->nachname} {$this->vorname}");
    }
}
