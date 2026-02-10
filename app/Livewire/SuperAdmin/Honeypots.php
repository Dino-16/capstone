<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\HoneypotSetting;
use App\Models\Admin\HoneypotLog;

class Honeypots extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\HandlesToasts;

    public $isEnabled = true;
    public $fieldName = 'secondary_email';

    public function mount()
    {
        $setting = HoneypotSetting::first();
        if ($setting) {
            $this->isEnabled = (bool) $setting->is_enabled;
            $this->fieldName = $setting->field_name;
        } else {
            HoneypotSetting::create(['is_enabled' => true, 'field_name' => 'secondary_email']);
        }
    }

    public function toggleHoneypot()
    {
        $this->isEnabled = !$this->isEnabled;
        $this->updateSettings();
    }
    
    public function updateFieldName()
    {
        $this->updateSettings();
        $this->toast('Honeypot field name updated.');
    }

    private function updateSettings()
    {
        HoneypotSetting::first()->update([
            'is_enabled' => $this->isEnabled,
            'field_name' => $this->fieldName,
        ]);
        
        $this->toast('Honeypot settings updated successfully.');
    }


    public function render()
    {
        $stats = [
            'total_trapped' => HoneypotLog::count(),
            'recent_traps' => HoneypotLog::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('livewire.superadmin.honeypots', [
            'logs' => HoneypotLog::latest()->paginate(10),
            'stats' => $stats
        ])->layout('layouts.app');
    }
    public function deleteLog($id)
    {
        HoneypotLog::find($id)?->delete();
        $this->toast('Log entry deleted successfully.');
    }

}
