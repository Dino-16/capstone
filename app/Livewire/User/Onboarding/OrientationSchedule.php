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

    #[Url]
    public $search = '';

    public $perPage = 10;
    public $showModal = false;
    public $showEditModal = false;
    public $showMessageModal = false;
    public $editingOrientationId = null;
    public $employees = [];
    public $filteredEmployees = [];
    public $showEmployeeDropdown = false;
    public $approvedFacilities = [];
    public $selectedFacility = '';

    // Form Properties
    // Form Properties
    public $employeeName;
    public $email;
    public $position; // <--- Added
    public $orientationDate;
    public $location;
    public $facilitator;
    public $notes;
    public $status = 'scheduled';

    public function updatedEmployeeName()
    {
        if (strlen($this->employeeName) < 2) {
            $this->employees = [];
            $this->showEmployeeDropdown = false;
            return;
        }

        try {
            $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees', [
                'search' => $this->employeeName,
                'per_page' => 10
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $rawEmployees = $data['data'] ?? $data ?? [];
                
                $this->employees = collect($rawEmployees)->map(function($emp) {
                    // Normalize name for checking in selectEmployee
                    $name = $emp['full_name'] ?? trim(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''));
                    $emp['name'] = $name;
                    $emp['employee_name'] = $name; 
                    return $emp;
                })->toArray();
                
                $this->filteredEmployees = $this->employees;

                $this->showEmployeeDropdown = count($this->employees) > 0;
            }
        } catch (\Exception $e) {
            $this->employees = [];
            $this->filteredEmployees = [];
        }
    }

    public function selectEmployee($id)
    {
        $employee = collect($this->employees)->first(function ($emp) use ($id) {
            return (string)($emp['id'] ?? '') === (string)$id;
        });

        if ($employee) {
            $this->employeeName = $employee['name'] ?? $employee['employee_name'] ?? '';
            $this->email = $employee['email'] ?? null;
            $this->position = $employee['position'] ?? null;
        }
        
        $this->showEmployeeDropdown = false;
        $this->filteredEmployees = $this->employees;
    }

    // ...

    public function openModal()
    {
        $this->loadApprovedFacilities();
        $this->resetValidation();
        $this->reset(['employeeName', 'email', 'position', 'orientationDate', 'location', 'facilitator', 'notes', 'status', 'selectedFacility']);
        $this->showModal = true;
    }

    public function loadApprovedFacilities()
    {
        try {
            $response = Http::withoutVerifying()->get('https://facilities-admin.jetlougetravels-ph.com/reservation_status_api.php');
            
            if ($response->successful()) {
                $data = $response->json();
                $allReservations = $data['reservations'] ?? [];
                
                // Filter for Approved ones only (following Applications logic)
                $this->approvedFacilities = collect($allReservations)
                    ->filter(function($res) {
                        $status = strtolower($res['status'] ?? '');
                        if ($status !== 'approved') {
                            return false;
                        }

                        $name = strtolower($res['requested_by'] ?? $res['full_name'] ?? '');
                        $dept = strtolower($res['department_name'] ?? $res['department'] ?? '');
                        
                        // Visible if status is approved AND name or department contains HR keywords
                        $hrKeywords = ['hr staff', 'hr manager', 'hr', 'human resource'];
                        
                        $matchesName = false;
                        foreach ($hrKeywords as $keyword) {
                            if (str_contains($name, $keyword)) {
                                $matchesName = true;
                                break;
                            }
                        }

                        $matchesDept = false;
                        foreach ($hrKeywords as $keyword) {
                            if (str_contains($dept, $keyword)) {
                                $matchesDept = true;
                                break;
                            }
                        }
                        
                        return $matchesName || $matchesDept;
                    })
                    ->map(function($res) {
                        return [
                            'id' => $res['request_id'] ?? $res['id'],
                            'name' => $res['facility_name'],
                            'location' => $res['location'],
                            'date' => $res['booking_date'],
                            'start_time' => $res['start_time'],
                            'details' => ($res['facility_name'] ?? 'Facility') . ' (' . ($res['location'] ?? 'N/A') . ')'
                        ];
                    })
                    ->values()
                    ->toArray();
            }
        } catch (\Exception $e) {
            $this->approvedFacilities = [];
        }
    }

    public function updatedSelectedFacility($value)
    {
        if ($value) {
            $facility = collect($this->approvedFacilities)->firstWhere('id', $value);
            if ($facility) {
                // Auto-fill location and date/time from the approved reservation
                $this->location = ($facility['name'] ?? 'Facility') . ' (' . ($facility['location'] ?? 'N/A') . ')';
                if (!empty($facility['date']) && !empty($facility['start_time'])) {
                    $this->orientationDate = date('Y-m-d\TH:i', strtotime($facility['date'] . ' ' . $facility['start_time']));
                }
            }
        }
    }

    // ...

    public function editOrientation($orientationId)
    {
        $this->loadApprovedFacilities();
        $orientation = Orientation::findOrFail($orientationId);
        $this->editingOrientationId = $orientationId;
        $this->employeeName = $orientation->employee_name;
        $this->email = $orientation->email;
        $this->position = $orientation->position; // <--- Added
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
            // ...
            $this->validate([
                'employeeName' => 'required|string|max:255|unique:orientations,employee_name',
                'email' => 'nullable|email|max:255',
                'position' => 'nullable|string|max:255', // <--- Added
                'orientationDate' => 'required|date',
                'location' => 'required|string|max:255',
                'facilitator' => 'required|string|max:255',
                'status' => 'required|in:scheduled,completed,cancelled',
            ]);

            // ...

            Orientation::create([
                'employee_name' => $this->employeeName,
                'email' => $this->email,
                'position' => $this->position, // <--- Added
                'orientation_date' => $this->orientationDate,
                'location' => $this->location,
                'facilitator' => $this->facilitator,
                'notes' => $this->notes,
                'status' => $this->status,
            ]);

            // ...
            $this->reset(['employeeName', 'email', 'position', 'orientationDate', 'location', 'facilitator', 'notes', 'status']);
            
        } catch (\Exception $e) {
            // ...
        }
    }

    public function updateOrientation()
    {
        try {
            $this->validate([
                'position' => 'nullable|string|max:255', // <--- Added
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
                'position' => $this->position, // <--- Added
            ]);

            // ...
            $this->reset(['employeeName', 'email', 'position', 'orientationDate', 'location', 'facilitator', 'notes', 'status', 'editingOrientationId']);
            
        } catch (\Exception $e) {
            // ...
        }
    }

    public function deleteOrientation($orientationId)
    {
        if (auth()->user()->role !== 'Super Admin') {
            session()->flash('status', 'Unauthorized action.');
            return;
        }

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
