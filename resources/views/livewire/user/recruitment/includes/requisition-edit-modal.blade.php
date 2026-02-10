{{-- Edit Modal --}}
@if($showEditModal)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title">Edit Requisition</h5>
                <button type="button" class="btn-close" wire:click="closeEditModal"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="updateRequisition">
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" wire:model="position">
                        @error('position') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" wire:model="department">
                        @error('department') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening</label>
                        <input type="number" class="form-control" wire:model="opening">
                        @error('opening') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Close</button>
                <button type="button" class="btn btn-primary" wire:click="updateRequisition">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endif
