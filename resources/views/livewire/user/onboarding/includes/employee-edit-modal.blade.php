{{-- Edit Employee Modal --}}
@if($showEditModal)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Employee</h5>
                <button type="button" class="btn-close" wire:click="closeEditModal"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="updateEmployee">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" wire:model="first_name">
                            @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" wire:model="last_name">
                            @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="email">
                            @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" wire:model="phone">
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" wire:model="position">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employment Status</label>
                            <select class="form-select" wire:model="employment_status">
                                <option value="regular">Regular</option>
                                <option value="new_hire">New Hire</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Close</button>
                <button type="button" class="btn btn-primary" wire:click="updateEmployee">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endif
