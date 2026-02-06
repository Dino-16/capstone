@if($showModal)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-plus"></i>
                    Schedule Orientation
                </h5>
                <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
            </div>

            <form wire:submit.prevent="addOrientation">
                <div class="modal-body p-4">
                    <div class="mb-4 position-relative">
                        <label class="form-label">Search Employee</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            wire:model.live="employeeName" 
                            placeholder="Type to search employees..."
                            autocomplete="off"
                        >
                        @error('employeeName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        
                        {{-- Employee Dropdown --}}
                        @if($showEmployeeDropdown && count($filteredEmployees) > 0)
                            <div class="position-absolute w-100 bg-white border rounded shadow mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                @foreach($filteredEmployees as $employee)
                                    <button 
                                        type="button"
                                        wire:key="emp-{{ $employee['id'] ?? $loop->index }}"
                                        class="w-100 text-start px-3 py-2 border-0 border-bottom bg-white"
                                        wire:click.prevent="selectEmployee('{{ $employee['id'] ?? '' }}')"
                                        onmouseover="this.style.backgroundColor='#f8f9fa'"
                                        onmouseout="this.style.backgroundColor='white'"
                                    >
                                        <div class="fw-bold">{{ $employee['name'] ?? $employee['employee_name'] ?? 'Unknown' }}</div>
                                        @if(isset($employee['department']))
                                            <small class="text-muted d-block">{{ is_array($employee['department']) ? ($employee['department']['name'] ?? 'N/A') : $employee['department'] }}</small>
                                        @endif
                                        @if(isset($employee['email']))
                                            <small class="text-secondary d-block">{{ $employee['email'] }}</small>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @elseif($showEmployeeDropdown && count($filteredEmployees) == 0)
                            <div class="position-absolute w-100 bg-white border rounded shadow mt-1" style="z-index: 1000;">
                                <div class="px-3 py-4 text-center text-muted">
                                    <i class="bi bi-person-x fs-3 d-block mx-auto mb-2"></i>
                                    Employee not found
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    wire:model="email" 
                                    placeholder="employee@example.com"
                                    readonly
                                >
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                <small class="text-muted">Auto-filled when selecting employee</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Position</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    wire:model="position" 
                                    placeholder="Job Position"
                                    readonly
                                >
                                @error('position') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Date & Time</label>
                                <input type="datetime-local" class="form-control" wire:model="orientationDate">
                                @error('orientationDate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Location / Facility <span class="text-danger">*</span></label>
                                <select class="form-select @error('location') is-invalid @enderror" 
                                        wire:model.live="selectedFacility">
                                    <option value="">-- Select Approved Facility --</option>
                                    @foreach($approvedFacilities as $facility)
                                        <option value="{{ $facility['id'] }}">{{ $facility['details'] }}</option>
                                    @endforeach
                                </select>
                                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Facilitator</label>
                                <input type="text" class="form-control" wire:model="facilitator" placeholder="John Smith">
                                @error('facilitator') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" wire:model="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <span wire:loading.remove wire:target="addOrientation"><i class="bi bi-check-lg me-1"></i> Schedule Orientation</span>
                        <span wire:loading wire:target="addOrientation" class="spinner-border spinner-border-sm"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif