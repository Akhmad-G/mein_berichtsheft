<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagesbericht extends Model
{
  protected $table = 'tagesberichte';

//casts() converts into a Carbon date object that has the following methods:
// - format(),
// - addDay(),
// - subWeek(),
// - isPast()

//  protected function casts(): array
//  {
//    return [
//      'datum' => 'date',
//    ];
//  }


//  Laravel accessors

  public function getTitleAttribute(): string
  {
    return $this->datum . ' Tagesbericht';
  }
  
  public function getSortDateAttribute(): string
  {
    return $this->datum;
  }
}
