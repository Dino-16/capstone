<?php

namespace App\Livewire\Traits;

trait HandlesToasts
{
    /**
     * Clear the status message from the session.
     * This is used by the x-toast component.
     */
    public function clearStatus()
    {
        session()->forget('status');
    }

    public function toast($message, $type = 'success')
    {
        session()->push('status', [
            'message' => $message,
            'type' => $type,
        ]);
    }
}
