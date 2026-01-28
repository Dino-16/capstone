<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Logout extends Component
{
    public $isDropdown = false;

    public function logout()
    {
        session()->forget('user');
        session()->flush();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.logout');
    }
}
