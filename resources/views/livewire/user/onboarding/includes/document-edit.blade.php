@if($showEditModal)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div @class('modal-dialog modal-lg modal-dialog-centered')>
        <div @class('modal-content border-0 shadow-lg')>
            <div @class('modal-header')>
                <h5 @class('modal-title fw-bold')>Edit Employee Documents</h5>
                <button type="button" @class('btn-close') wire:click="$set('showEditModal', false)"></button>
            </div>

            <form wire:submit.prevent="updateEmployee">
                <div @class('modal-body p-4')>
                    <div @class('mb-3')>
                        <label @class('form-label fw-bold')>Employee Name</label>
                        <input type="text" @class('form-control') value="{{ $employeeName }}" readonly>
                    </div>

                    <div @class('mb-3')>
                        <label @class('form-label fw-bold')>Email</label>
                        <input type="email" @class('form-control') wire:model="email" placeholder="employee@example.com">
                        </select>
                    </div>

                    <div @class('mb-4')>
                        <label @class('form-label fw-bold')>Select Documents</label>
                        <div @class('border rounded p-3 bg-light')>
                            <div @class('row g-2')>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="edit_resume"
                                            wire:model.live="selectedDocuments"
                                            value="resume"
                                        >
                                        <label @class('form-check-label') for="edit_resume">
                                            <strong>Resume</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="edit_medical"
                                            wire:model.live="selectedDocuments"
                                            value="medical_certificate"
                                        >
                                        <label @class('form-check-label') for="edit_medical">
                                            <strong>Medical Certificate</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="edit_gov_id"
                                            wire:model.live="selectedDocuments"
                                            value="valid_government_id"
                                        >
                                        <label @class('form-check-label') for="edit_gov_id">
                                            <strong>Valid Government ID</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="edit_transcript"
                                            wire:model.live="selectedDocuments"
                                            value="transcript_of_records"
                                        >
                                        <label @class('form-check-label') for="edit_transcript">
                                            <strong>Transcript of Records</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="edit_nbi"
                                            wire:model.live="selectedDocuments"
                                            value="nbi_clearance"
                                        >
                                        <label @class('form-check-label') for="edit_nbi">
                                            <strong>NBI Clearance</strong>
                                        </label>
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('form-check')>
                                        <input 
                                            @class('form-check-input') 
                                            type="checkbox" 
                                            id="edit_barangay"
                                            wire:model.live="selectedDocuments"
                                            value="barangay_clearance"
                                        >
                                        <label @class('form-check-label') for="edit_barangay">
                                            <strong>Barangay Clearance</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small @class('text-muted')>Check documents to include them. </small>
                    </div>

                    <div @class('mb-3')>
                        <div @class(['alert', 'alert-success' => count($selectedDocuments) === 6, 'alert-warning' => count($selectedDocuments) !== 6])>
                            <strong>Status: </strong>
                            @if(count($selectedDocuments) === 6)
                                <span @class('fw-bold')>Complete</span> - All documents selected
                            @else
                                <span @class('fw-bold')>Incomplete</span> - {{ count($selectedDocuments) }}/6 documents selected
                            @endif
                        </div>
                    </div>

                    <div @class('mb-3')>
                        <label @class('form-label fw-bold')>Notes (Optional)</label>
                        <textarea @class('form-control') wire:model="notes" rows="3" placeholder="Add notes about this employee..."></textarea>
                    </div>
                </div>

                <div @class('modal-footer')>
                    <button type="button" @class('btn btn-secondary') wire:click="$set('showEditModal', false)">Cancel</button>
                    <button type="submit" @class('btn btn-primary')>
                        <span wire:loading.remove wire:target="updateEmployee">Update Employee</span>
                        <span wire:loading wire:target="updateEmployee" @class('spinner-border spinner-border-sm')></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif