<?php

namespace App\Enums;

/**
 * Represents the role of a user in the system.
 */
enum UserRole: string {
  case Azubi = 'azubi';
  case Ausbilder = 'ausbilder';

  /**
   * Get the human-readable UI label in German.
   *
   * @return string
   */
  public function label(): string {
    return match ($this) {
      self::Azubi => 'Auszubildende(r)',
      self::Ausbilder => 'Ausbilder(in)',
    };
  }
}
