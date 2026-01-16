<?php

namespace App\Livewire\User\Onboarding;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\Onboarding\Orientation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OrientationSchedule extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $search = '';

    public $perPage = 10;
    public $showModal = false;
    public $showEditModal = false;
    public $showMessageModal = false;
    public $editingOrientationId = null;
    public $employees = [];
    public $filteredEmployees = [];
    public $showEmployeeDropdown = false;

    // Form Properties
    public $employeeName;
    public $email;
    public $orientationDate;
    public $location;
    public $facilitator;
    public $notes;
    public $status = 'scheduled';
    
    // Message Properties
    public $messageOrientation;
    public $messageSubject;
    public $messageContent;

    public function mount()
    {
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees');

        if ($response->successful() && is_array($response->json())) {
            $this->employees = $response->json();
            $this->filteredEmployees = $this->employees;
        } else {
            $this->employees = [];
            $this->filteredEmployees = [];
        }
    }

    public function updatedEmployeeName()
    {
        if (empty($this->employeeName)) {
            $this->filteredEmployees = $this->employees;
            $this->showEmployeeDropdown = false;
            return;
        }

        $searchTerm = strtolower($this->employeeName);
        $this->filteredEmployees = collect($this->employees)
            ->filter(function ($employee) use ($searchTerm) {
                $name = strtolower($employee['name'] ?? $employee['employee_name'] ?? '');
                return str_contains($name, $searchTerm);
            })
            ->take(10)
            ->values()
            ->toArray();
        
        $this->showEmployeeDropdown = true;
    }

    public function selectEmployee($employeeName)
    {
        $this->employeeName = $employeeName;
        
        // Find the employee and get their email
        $employee = collect($this->employees)->first(function ($emp) use ($employeeName) {
            $name = $emp['name'] ?? $emp['employee_name'] ?? '';
            return $name === $employeeName;
        });
        
        $this->email = $employee['email'] ?? null;
        
        $this->showEmployeeDropdown = false;
        $this->filteredEmployees = $this->employees;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['employeeName', 'email', 'orientationDate', 'location', 'facilitator', 'notes', 'status']);
        $this->showModal = true;
    }

    public function editOrientation($orientationId)
    {
        $orientation = Orientation::findOrFail($orientationId);
        $this->editingOrientationId = $orientationId;
        $this->employeeName = $orientation->employee_name;
        $this->email = $orientation->email;
        $this->orientationDate = $orientation->orientation_date->format('Y-m-d\TH:i');
        $this->location = $orientation->location;
        $this->facilitator = $orientation->facilitator;
        $this->notes = $orientation->notes;
        $this->status = $orientation->status;
        $this->showEditModal = true;
    }

    public function addOrientation()
    {
        try {
            // Debug: Log incoming data
            logger('Add Orientation Data:', [
                'employeeName' => $this->employeeName,
                'email' => $this->email,
                'orientationDate' => $this->orientationDate,
                'location' => $this->location,
                'facilitator' => $this->facilitator,
                'status' => $this->status,
                'notes' => $this->notes
            ]);

            $this->validate([
                'employeeName' => 'required|string|max:255|unique:orientations,employee_name',
                'email' => 'nullable|email|max:255',
                'orientationDate' => 'required|date',
                'location' => 'required|string|max:255',
                'facilitator' => 'required|string|max:255',
                'status' => 'required|in:scheduled,completed,cancelled',
            ]);

            logger('Validation passed, creating orientation...');

            Orientation::create([
                'employee_name' => $this->employeeName,
                'email' => $this->email,
                'orientation_date' => $this->orientationDate,
                'location' => $this->location,
                'facilitator' => $this->facilitator,
                'notes' => $this->notes,
                'status' => $this->status,
            ]);

            logger('Orientation created successfully');

            // Send email notification if email is provided
            if ($this->email) {
                $this->sendOrientationEmail($this->email, $this->employeeName, $this->orientationDate, $this->location, $this->facilitator);
            }

            session()->flash('status', 'Orientation scheduled successfully for ' . $this->employeeName);
            $this->showModal = false;
            $this->reset(['employeeName', 'email', 'orientationDate', 'location', 'facilitator', 'notes', 'status']);
            
        } catch (\Exception $e) {
            logger('Add Orientation Error: ' . $e->getMessage());
            session()->flash('error', 'Error scheduling orientation: ' . $e->getMessage());
        }
    }

    public function updateOrientation()
    {
        try {
            $this->validate([
                'orientationDate' => 'required|date',
                'location' => 'required|string|max:255',
                'facilitator' => 'required|string|max:255',
                'status' => 'required|in:scheduled,completed,cancelled',
            ]);

            $orientation = Orientation::findOrFail($this->editingOrientationId);
            
            $orientation->update([
                'orientation_date' => $this->orientationDate,
                'location' => $this->location,
                'facilitator' => $this->facilitator,
                'notes' => $this->notes,
                'status' => $this->status,
                'email' => $this->email,
            ]);

            // Send email notification if status changed to scheduled and email is provided
            if ($this->status === 'scheduled' && $this->email) {
                $this->sendOrientationEmail($this->email, $this->employeeName, $this->orientationDate, $this->location, $this->facilitator);
            }

            session()->flash('status', 'Orientation updated successfully.');
            $this->showEditModal = false;
            $this->reset(['employeeName', 'email', 'orientationDate', 'location', 'facilitator', 'notes', 'status', 'editingOrientationId']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating orientation: ' . $e->getMessage());
        }
    }

    public function deleteOrientation($orientationId)
    {
        Orientation::findOrFail($orientationId)->delete();
        session()->flash('status', 'Orientation deleted successfully.');
    }

    public function openMessageModal($orientationId)
    {
        $orientation = Orientation::findOrFail($orientationId);
        $this->messageOrientation = $orientation;
        $this->messageSubject = '';
        $this->messageContent = '';
        $this->showMessageModal = true;
    }

    public function sendMessage()
    {
        try {
            $this->validate([
                'messageSubject' => 'required|string|max:255',
                'messageContent' => 'required|string|min:10',
            ]);

            if (!$this->messageOrientation->email) {
                session()->flash('error', 'Employee does not have an email address.');
                return;
            }

            Mail::raw($this->messageContent, function ($message) {
                $message->to($this->messageOrientation->email)
                    ->subject($this->messageSubject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            session()->flash('status', 'Message sent to ' . $this->messageOrientation->employee_name . ' at ' . $this->messageOrientation->email);
            $this->showMessageModal = false;
            $this->reset(['messageSubject', 'messageContent', 'messageOrientation']);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending message: ' . $e->getMessage());
        }
    }

    private function sendOrientationEmail($email, $employeeName, $orientationDate, $location, $facilitator)
    {
        try {
            $subject = "Orientation Schedule - " . config('app.name');
            $content = "Dear {$employeeName},\n\n";
            $content .= "Your orientation has been scheduled with the following details:\n\n";
            $content .= "Date: " . $orientationDate . "\n";
            $content .= "Location: {$location}\n";
            $content .= "Facilitator: {$facilitator}\n\n";
            $content .= "Please be on time and bring any required documents.\n\n";
            $content .= "Best regards,\n";
            $content .= config('mail.from.name');

            Mail::raw($content, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send orientation email: ' . $e->getMessage());
        }
    }

    public function clearStatus()
    {
        session()->forget(['status', 'error']);
    }

    public function export()
    {
        $export = new \App\Exports\Onboarding\OrientationSchedulesExport();
        return $export->export();
    }

    public function render()
    {
        $query = Orientation::query();

        if ($this->search) {
            $query->where('employee_name', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhere('facilitator', 'like', '%' . $this->search . '%');
        }

        return view('livewire.user.onboarding.orientation-schedule', [
            'orientations' => $query->latest()->paginate($this->perPage),
        ])->layout('layouts.app');
    }
}
