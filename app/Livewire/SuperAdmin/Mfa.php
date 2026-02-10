<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\MfaSetting;
use App\Models\Admin\MfaLog;

class Mfa extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\HandlesToasts;

    public $isGlobalEnabled = true;
    public $isHrStaffEnabled = true;
    public $isHrManagerEnabled = true;
    public $isSuperAdminEnabled = true;

    public $showDeleteModal = false;
    public $deletingLogId = null;
    public $showDeleteAllModal = false;

    public function mount()
    {
        $setting = MfaSetting::first();
        if ($setting) {
            $this->isGlobalEnabled = (bool) $setting->is_global_enabled;
            $this->isHrStaffEnabled = (bool) $setting->hr_staff_enabled;
            $this->isHrManagerEnabled = (bool) $setting->hr_manager_enabled;
            $this->isSuperAdminEnabled = (bool) $setting->super_admin_enabled;
        } else {
            MfaSetting::create([
                'is_global_enabled' => true, 
                'hr_staff_enabled' => true, 
                'hr_manager_enabled' => true,
                'super_admin_enabled' => true
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

    public function toggleSuperAdmin()
    {
        $this->isSuperAdminEnabled = !$this->isSuperAdminEnabled;
        $this->updateSettings();
    }

    private function updateSettings()
    {
        MfaSetting::first()->update([
            'is_global_enabled' => $this->isGlobalEnabled,
            'hr_staff_enabled' => $this->isHrStaffEnabled,
            'hr_manager_enabled' => $this->isHrManagerEnabled,
            'super_admin_enabled' => $this->isSuperAdminEnabled,
        ]);
        
        $this->toast('MFA settings updated successfully.');
    }


    public function render()
    {
        $stats = [
            'total_verified' => MfaLog::where('status', 'success')->where('action', 'mfa_verified')->count(),
            'total_failed' => MfaLog::where('status', 'failed')->count(),
            'total_logs' => MfaLog::count(),
        ];

        return view('livewire.superadmin.mfa', [
            'logs' => MfaLog::latest()->paginate(10),
            'stats' => $stats
        ])->layout('layouts.app');
    }
    public function confirmDeleteLog($id)
    {
        $this->deletingLogId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLog()
    {
        if ($this->deletingLogId) {
            MfaLog::find($this->deletingLogId)?->delete();
            $this->toast('Log entry deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->deletingLogId = null;
    }

    public function confirmDeleteAll()
    {
        $this->showDeleteAllModal = true;
    }

    public function deleteAllLogs()
    {
        MfaLog::truncate();
        $this->toast('All log entries deleted successfully.');
        $this->showDeleteAllModal = false;
    }

}
