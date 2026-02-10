{{-- Contract Approval Modal --}}
@if($showApprovalModal)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title"><i class="bi bi-file-earmark-lock-fill me-2"></i>Submit Contract for Approval</h5>
                <button type="button" class="btn-close" wire:click="closeApprovalModal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Submit a drafted contract to the Legal Department for review and final approval.
                </div>

                <div class="row g-3">
                    {{-- Contract Info --}}
                    <div class="col-12">
                         <label class="form-label fw-bold small text-uppercase text-secondary">Contract Title <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" wire:model="approvalTitle" placeholder="e.g. Service Agreement 2024">
                         @error('approvalTitle') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Client/Requestor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="approvalClientName">
                        @error('approvalClientName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Client/Requestor Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" wire:model="approvalClientEmail">
                        @error('approvalClientEmail') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Contract Type <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="approvalType">
                            <option value="">Select Type</option>
                            <option value="service_agreement">Service Agreement</option>
                            <option value="employment_contract">Employment Contract</option>
                            <option value="partnership_agreement">Partnership Agreement</option>
                            <option value="vendor_contract">Vendor Contract</option>
                            <option value="non_disclosure_agreement">Non-Disclosure Agreement</option>
                            <option value="lease_agreement">Lease Agreement</option>
                            <option value="other">Other</option>
                        </select>
                        @error('approvalType') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Contract Value (PHP) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" wire:model="approvalValue" placeholder="0.00">
                        @error('approvalValue') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" wire:model="approvalStartDate">
                        @error('approvalStartDate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-secondary">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" wire:model="approvalEndDate">
                        @error('approvalEndDate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Description</label>
                        <textarea class="form-control" rows="3" wire:model="approvalDescription" placeholder="Optional notes..."></textarea>
                        @error('approvalDescription') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- File Upload --}}
                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Attach Contract File <span class="text-danger">*</span></label>
                        <div class="border rounded p-3 bg-white">
                            <input type="file" class="form-control" wire:model="approvalFile" accept=".pdf,.doc,.docx">
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-file-earmark-arrow-up"></i> Accepted Formats: PDF, DOC, DOCX (Max 10MB)
                            </small>
                        </div>
                        @error('approvalFile') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="approvalFile" class="text-success small mt-2">
                            <span class="spinner-border spinner-border-sm me-2"></span>Uploading file...
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary px-4" wire:click="closeApprovalModal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" wire:click="submitContractApproval" wire:loading.attr="disabled" wire:target="submitContractApproval, approvalFile">
                    <span wire:loading.remove wire:target="submitContractApproval"><i class="bi bi-send-fill me-2"></i>Submit for Approval</span>
                    <span wire:loading wire:target="submitContractApproval"><span class="spinner-border spinner-border-sm me-2"></span>Submitting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
