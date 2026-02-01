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
                                    <div 
                                        class="px-3 py-2 border-bottom cursor-pointer"
                                        style="cursor: pointer;"
                                        wire:click="selectEmployee('{{ $employee['name'] ?? $employee['employee_name'] ?? 'Unknown' }}')"
                                        onmouseover="this.style.backgroundColor='#f8f9fa'"
                                        onmouseout="this.style.backgroundColor='white'"
                                    >
                                        <strong>{{ $employee['name'] ?? $employee['employee_name'] ?? 'Unknown' }}</strong>
                                        @if(isset($employee['department']))
                                            <br><small class="text-muted">{{ is_array($employee['department']) ? ($employee['department']['name'] ?? 'N/A') : $employee['department'] }}</small>
                                        @endif
                                    </div>
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
                                <label class="form-label">Date & Time</label>
                                <input type="datetime-local" class="form-control" wire:model="orientationDate">
                                @error('orientationDate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" wire:model="location" placeholder="Conference Room A">
                                @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Facilitator</label>
                                <input type="text" class="form-control" wire:model="facilitator" placeholder="John Smith">
                                @error('facilitator') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="scheduled">Scheduled</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
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