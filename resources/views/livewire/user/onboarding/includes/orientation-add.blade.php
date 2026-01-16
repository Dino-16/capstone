@if($showModal)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div @class('modal-dialog modal-lg modal-dialog-centered')>
        <div @class('modal-content border-0 shadow-lg')>
            <div @class('modal-header')>
                <h5 @class('modal-title fw-bold')>Schedule Orientation</h5>
                <button type="button" @class('btn-close') wire:click="$set('showModal', false)"></button>
            </div>

            <form wire:submit.prevent="addOrientation">
                <div @class('modal-body p-4')>
                    <div @class('mb-4 position-relative')>
                        <label @class('form-label fw-bold')>Search Employee</label>
                        <input 
                            type="text" 
                            @class('form-control') 
                            wire:model.live="employeeName" 
                            placeholder="Type to search employees..."
                            autocomplete="off"
                        >
                        @error('employeeName') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        
                        {{-- Employee Dropdown --}}
                        @if($showEmployeeDropdown && count($filteredEmployees) > 0)
                            <div @class('position-absolute w-100 bg-white border rounded shadow-lg mt-1') style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                @foreach($filteredEmployees as $employee)
                                    <div 
                                        @class('px-3 py-2 hover:bg-light cursor-pointer')
                                        wire:click="selectEmployee('{{ $employee['name'] ?? $employee['employee_name'] ?? 'Unknown' }}')"
                                    >
                                        <strong>{{ $employee['name'] ?? $employee['employee_name'] ?? 'Unknown' }}</strong>
                                        @if(isset($employee['department']))
                                            <br><small @class('text-muted')>{{ $employee['department'] }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($showEmployeeDropdown && count($filteredEmployees) == 0)
                            <div @class('position-absolute w-100 bg-white border rounded shadow-lg mt-1') style="z-index: 1000;">
                                <div @class('px-3 py-4 text-center text-muted')>
                                    <i @class('bi bi-person-x fs-3 d-block mx-auto mb-2')></i>
                                    Employee not found
                                </div>
                            </div>
                        @endif
                    </div>

                    <div @class('mb-4')>
                        <label @class('form-label fw-bold')>Email</label>
                        <input 
                            type="email" 
                            @class('form-control') 
                            wire:model="email" 
                            placeholder="employee@example.com"
                            readonly
                        >
                        @error('email') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        <small @class('text-muted')>Email is automatically filled when you select an employee</small>
                    </div>

                    <div @class('row')>
                        <div @class('col-md-6')>
                            <div @class('mb-4')>
                                <label @class('form-label fw-bold')>Date & Time</label>
                                <input type="datetime-local" @class('form-control') wire:model="orientationDate">
                                @error('orientationDate') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div @class('col-md-6')>
                            <div @class('mb-4')>
                                <label @class('form-label fw-bold')>Location</label>
                                <input type="text" @class('form-control') wire:model="location" placeholder="Conference Room A">
                                @error('location') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div @class('mb-4')>
                        <label @class('form-label fw-bold')>Facilitator</label>
                        <input type="text" @class('form-control') wire:model="facilitator" placeholder="John Smith">
                        @error('facilitator') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                    </div>

                    <div @class('mb-4')>
                        <label @class('form-label fw-bold')>Status</label>
                        <select @class('form-select') wire:model="status">
                            <option value="scheduled">Scheduled</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('status') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                    </div>

                    <div @class('mb-4')>
                        <label @class('form-label fw-bold')>Notes</label>
                        <textarea @class('form-control') wire:model="notes" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                </div>

                <div @class('modal-footer')>
                    <button type="button" @class('btn btn-secondary') wire:click="$set('showModal', false)">Cancel</button>
                    <button type="submit" @class('btn btn-primary')>
                        <span wire:loading.remove wire:target="addOrientation">Schedule Orientation</span>
                        <span wire:loading wire:target="addOrientation" @class('spinner-border spinner-border-sm')></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif