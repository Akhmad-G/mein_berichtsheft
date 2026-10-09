<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Contracts\GitLabServiceInterface;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable([
  'email', 'password', 'vorname', 'nachname', 'ausbildungsberuf',
  'ausbildungsbetrieb', 'abteilung', 'ausbildungsbeginn', 'role', 'ausbilder_id',])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable {
  /** @use HasFactory<UserFactory> */
  use HasFactory, Notifiable;

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array {
    return [
      'ausbildungsbeginn' => 'date',
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'role' => UserRole::class,];
  }

  //?  No usages? What about: Http/Controllers/Auth/RegisteredUserController.php->store()
  public function assignGitLabPath(GitLabServiceInterface $gitLabService): void {
    if (!$this->isAzubi()) {
      return;
    }

    if ($this->gitlab_path) {
      return;
    }

    if (!$this->vorname && !$this->nachname) {
      return;
    }

    $basePath = Str::slug($this->nameReversed(), '-', 'de');
    $path = $basePath;
    $counter = 2;

    while (!$this->isGitLabPathAvailable($path, $gitLabService)) {
      $path = "{$basePath}-{$counter}";
      $counter++;
    }

    $this->gitlab_path = $path;
    $this->saveQuietly();
  }

  private function isGitLabPathAvailable(string $path, GitLabServiceInterface $gitLabService): bool {
    $existsInDatabase = static::query()
      ->whereKeyNot($this->getKey())
      ->where('gitlab_path', $path)
      ->exists();

    return !$existsInDatabase && !$gitLabService->pathExists($path);
  }

  protected function name(): Attribute {
    return Attribute::make(get: fn() => trim("{$this->vorname} {$this->nachname}"));
  }

  public function nameReversed(): string {
    return trim("{$this->nachname} {$this->vorname}");
  }

  protected function initials(): Attribute {
    return Attribute::make(get:
      fn() => mb_strtoupper(mb_substr($this->vorname ?? '', 0, 1) . mb_substr($this->nachname ?? '', 0, 1)));
  }

  public function ausbilder(): BelongsTo {
    return $this->belongsTo(User::class, 'ausbilder_id');
  }

  public function azubis(): HasMany {
    return $this->hasMany(User::class, 'ausbilder_id');
  }

  public function isAzubi(): bool {
    return $this->role === UserRole::Azubi;
  }

  public function isAusbilder(): bool {
    return $this->role === UserRole::Ausbilder;
  }

// TODO AusbildungsPeriod
  public function trainingPeriod(): ?string {
    return $this->ausbildungsbeginn ? 'seit ' . $this->ausbildungsbeginn->format('d.m.Y') : null;
  }
}
