<?php

namespace App\Support;

class GitLabPath
{
  public static function encode(string $path): string
  {
    return rtrim(strtr(base64_encode($path), '+/', '-_'), '=');
  }
  
  public static function decode(string $encoded): string
  {
    $encoded = strtr($encoded, '-_', '+/');
    $encoded .= str_repeat('=', (4 - strlen($encoded) % 4) % 4);
    return base64_decode($encoded);
  }
}