<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * View component for rendering the guest application layout.
 */
class GuestLayout extends Component {
  /**
   * Get the view / contents that represents the component.
   *
   * @return View
   */
  public function render(): View {
    return view('layouts.guest');
  }
}
