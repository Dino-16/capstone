<?php

namespace App\Livewire\Setting;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        $user = session('user');
        return view('livewire.setting.profile', ['user' => $user])->layout('layouts.app');
    }
}
