@if($showModal)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div @class('modal-dialog modal-lg modal-dialog-centered')>
        <div @class('modal-content border-0 shadow-lg')>
            <div @class('modal-header')>
                <h5 @class('modal-title fw-bold')>Add Employee</h5>
                <button type="button" @class('btn-close') wire:click="$set('showModal', false)"></button>
            </div>

            <form wire:submit.prevent="addEmployee">
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

                    <div @class('mb-4')>
                        <label @class('form-label fw-bold')>Select Documents to Add</label>
                        <div @class('border rounded p-3 bg-light')>
                            <div @class('row g-2')>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="add_resume"
                                            wire:model.live="selectedDocuments"
                                            value="resume"
                                        >
                                        <label @class('form-check-label') for="add_resume">
                                            <strong>Resume</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="add_medical"
                                            wire:model.live="selectedDocuments"
                                            value="medical_certificate"
                                        >
                                        <label @class('form-check-label') for="add_medical">
                                            <strong>Medical Certificate</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="add_gov_id"
                                            wire:model.live="selectedDocuments"
                                            value="valid_government_id"
                                        >
                                        <label @class('form-check-label') for="add_gov_id">
                                            <strong>Valid Government ID</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="add_transcript"
                                            wire:model.live="selectedDocuments"
                                            value="transcript_of_records"
                                        >
                                        <label @class('form-check-label') for="add_transcript">
                                            <strong>Transcript of Records</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="add_nbi"
                                            wire:model.live="selectedDocuments"
                                            value="nbi_clearance"
                                        >
                                        <label @class('form-check-label') for="add_nbi">
                                            <strong>NBI Clearance</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="add_barangay"
                                            wire:model.live="selectedDocuments"
                                            value="barangay_clearance"
                                        >
                                        <label @class('form-check-label') for="add_barangay">
                                            <strong>Barangay Clearance</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small @class('text-muted')>Select which documents to add for this employee</small>
                    </div>

                    <div @class('mb-3')>
                        <label @class('form-label fw-bold')>Notes (Optional)</label>
                        <textarea @class('form-control') wire:model="notes" rows="3" placeholder="Add notes about this employee..."></textarea>
                    </div>
                </div>

                <div @class('modal-footer')>
                    <button type="button" @class('btn btn-secondary') wire:click="$set('showModal', false)">Cancel</button>
                    <button type="submit" @class('btn btn-primary')>
                        <span wire:loading.remove wire:target="addEmployee">Add Employee</span>
                        <span wire:loading wire:target="addEmployee" @class('spinner-border spinner-border-sm')></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif