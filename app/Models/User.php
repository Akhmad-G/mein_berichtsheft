<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Contracts\GitLabServiceInterface;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * User model representing apprentices (Azubis) and trainers (Ausbilder).
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string|null $vorname
 * @property string|null $nachname
 * @property string|null $ausbildungsberuf
 * @property string|null $ausbildungsbetrieb
 * @property string|null $abteilung
 * @property Carbon|null $ausbildungsbeginn
 * @property UserRole $role
 * @property int|null $ausbilder_id
 * @property string|null $gitlab_path
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $name
 * @property-read string $initials
 * @property-read User|null $ausbilder
 * @property-read Collection<int, User> $azubis
 */
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

  /**
   * Automatically generate and assign a unique GitLab directory path for an apprentice.
   *
   * @param GitLabServiceInterface $gitLabService
   * @return void
   */
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

  /**
   * Check if a candidate GitLab path is available (not taken in DB or GitLab).
   *
   * @param string $path
   * @param GitLabServiceInterface $gitLabService
   * @return bool
   */
  private function isGitLabPathAvailable(string $path, GitLabServiceInterface $gitLabService): bool {
    $existsInDatabase = static::query()
      ->whereKeyNot($this->getKey())
      ->where('gitlab_path', $path)
      ->exists();

    return !$existsInDatabase && !$gitLabService->pathExists($path);
  }

  /**
   * Get the full formatted name attribute (Vorname Nachname).
   *
   * @return Attribute<string, never>
   */
  protected function name(): Attribute {
    return Attribute::make(get: fn() => trim("{$this->vorname} {$this->nachname}"));
  }

  /**
   * Get the reversed full name (Nachname Vorname).
   *
   * @return string
   */
  public function nameReversed(): string {
    return trim("{$this->nachname} {$this->vorname}");
  }

  /**
   * Get the user initials attribute (e.g. "AG").
   *
   * @return Attribute<string, never>
   */
  protected function initials(): Attribute {
    return Attribute::make(get:
      fn() => mb_strtoupper(mb_substr($this->vorname ?? '', 0, 1) . mb_substr($this->nachname ?? '', 0, 1)));
  }

  /**
   * Relation to the assigned trainer (Ausbilder).
   *
   * @return BelongsTo<User, $this>
   */
  public function ausbilder(): BelongsTo {
    return $this->belongsTo(User::class, 'ausbilder_id');
  }

  /**
   * Relation to the apprentices supervised by this trainer.
   *
   * @return HasMany<User, $this>
   */
  public function azubis(): HasMany {
    return $this->hasMany(User::class, 'ausbilder_id');
  }

  /**
   * Determine if the user is an apprentice (Azubi).
   *
   * @return bool
   */
  public function isAzubi(): bool {
    return $this->role === UserRole::Azubi;
  }

  /**
   * Determine if the user is a trainer (Ausbilder).
   *
   * @return bool
   */
  public function isAusbilder(): bool {
    return $this->role === UserRole::Ausbilder;
  }

  /**
   * Get a human-readable training period string (e.g. "seit 01.09.2024").
   *
   * @return string|null
   */
  public function trainingPeriod(): ?string {
    return $this->ausbildungsbeginn ? 'seit ' . $this->ausbildungsbeginn->format('d.m.Y') : null;
  }
}
