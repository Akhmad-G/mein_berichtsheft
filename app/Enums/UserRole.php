<?php

namespace App\Enums;

enum UserRole: string
{
  case Azubi = 'azubi';
  case Ausbilder = 'ausbilder';
  
  public function label(): string
  {
    return match ($this) {
      self::Azubi => 'Auszubildende(r)',
      self::Ausbilder => 'Ausbilder(in)',
    };
  }
}
