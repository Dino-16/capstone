<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\RecaptchaSetting;
use App\Models\Admin\RecaptchaLog;

class Recaptcha extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\HandlesToasts;

    public $isEnabled = true;

    public function mount()
    {
        $setting = RecaptchaSetting::first();
        if ($setting) {
            $this->isEnabled = (bool) $setting->is_enabled;
        } else {
            // Create default if missing
            RecaptchaSetting::create(['is_enabled' => true]);
            $this->isEnabled = true;
        }
    }

    public function toggleRecaptcha()
    {
        $this->isEnabled = !$this->isEnabled;
        
        $setting = RecaptchaSetting::first();
        if ($setting) {
            $setting->update(['is_enabled' => $this->isEnabled]);
        } else {
            RecaptchaSetting::create(['is_enabled' => $this->isEnabled]);
        }

        $this->toast('reCAPTCHA setting updated successfully.');
    }


    public function render()
    {
        $stats = [
            'total_success' => RecaptchaLog::where('status', 'success')->count(),
            'total_failed' => RecaptchaLog::where('status', 'failed')->count(),
            'recent_attempts' => RecaptchaLog::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('livewire.superadmin.recaptcha', [
            'logs' => RecaptchaLog::latest()->paginate(10),
            'stats' => $stats
        ])->layout('layouts.app'); // Assuming this uses the main admin layout
    }
    public function deleteLog($id)
    {
        RecaptchaLog::find($id)?->delete();
        $this->toast('Log entry deleted successfully.');
    }

}
