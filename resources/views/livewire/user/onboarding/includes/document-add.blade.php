@if($showModal)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus"></i>
                    Add Employee
                </h5>
                <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
            </div>

            <form wire:submit.prevent="addEmployee">
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
                        <small class="text-muted">Email is automatically filled when you select an employee</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Select Documents to Add</label>
                        <div class="border rounded p-3">
                            <div class="row g-2">
                                @foreach($documentTypes as $docKey => $docLabel)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input" 
                                                type="checkbox" 
                                                id="add_{{ $docKey }}"
                                                wire:model.live="selectedDocuments"
                                                value="{{ $docKey }}"
                                            >
                                            <label class="form-check-label" for="add_{{ $docKey }}">
                                                {{ $docLabel }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @error('selectedDocuments') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        <small class="text-muted">Select which documents to add for this employee ({{ count($selectedDocuments) }}/6 selected)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" wire:model="notes" rows="3" placeholder="Add notes about this employee..."></textarea>
                        @error('notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <span wire:loading.remove wire:target="addEmployee"><i class="bi bi-check-lg me-1"></i> Add Employee</span>
                        <span wire:loading wire:target="addEmployee" class="spinner-border spinner-border-sm"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif