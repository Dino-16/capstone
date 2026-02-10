{{-- Delete Confirmation Modal --}}
@if($showDeleteModal)
<div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this checklist?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-danger" wire:click="deleteEmployee">Delete</button>
            </div>
        </div>
    </div>
</div>
@endif
