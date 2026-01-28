<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\MfaSetting;
use App\Models\Admin\MfaLog;

class Mfa extends Component
{
    use WithPagination;

    public $isGlobalEnabled = true;
    public $isHrStaffEnabled = true;
    public $isHrManagerEnabled = true;

    public function mount()
    {
        $setting = MfaSetting::first();
        if ($setting) {
            $this->isGlobalEnabled = (bool) $setting->is_global_enabled;
            $this->isHrStaffEnabled = (bool) $setting->hr_staff_enabled;
            $this->isHrManagerEnabled = (bool) $setting->hr_manager_enabled;
        } else {
            MfaSetting::create([
                'is_global_enabled' => true, 
                'hr_staff_enabled' => true, 
                'hr_manager_enabled' => true
            ]);
        }
    }

    public function toggleGlobal()
    {
        $this->isGlobalEnabled = !$this->isGlobalEnabled;
        $this->updateSettings();
    }

    public function toggleHrStaff()
    {
        $this->isHrStaffEnabled = !$this->isHrStaffEnabled;
        $this->updateSettings();
    }

    public function toggleHrManager()
    {
        $this->isHrManagerEnabled = !$this->isHrManagerEnabled;
        $this->updateSettings();
    }

    private function updateSettings()
    {
        MfaSetting::first()->update([
            'is_global_enabled' => $this->isGlobalEnabled,
            'hr_staff_enabled' => $this->isHrStaffEnabled,
            'hr_manager_enabled' => $this->isHrManagerEnabled,
        ]);
        
        session()->flash('message', 'MFA settings updated successfully.');
    }

    public function render()
    {
        $stats = [
            'total_verified' => MfaLog::where('status', 'success')->where('action', 'mfa_verified')->count(),
            'total_failed' => MfaLog::where('status', 'failed')->where('action', 'mfa_failed')->count(),
            'login_attempts' => MfaLog::where('action', 'login_attempt')->count(),
        ];

        return view('livewire.admin.mfa', [
            'logs' => MfaLog::latest()->paginate(10),
            'stats' => $stats
        ])->layout('layouts.app');
    }
}
