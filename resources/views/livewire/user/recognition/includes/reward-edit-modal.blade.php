<!-- Add/Edit Reward Modal -->
@if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title">Edit Reward</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>

                <form wire:submit="updateReward">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reward Name *</label>
                            <input type="text" @class('form-control') wire:model="name" placeholder="Enter reward name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description *</label>
                            <textarea @class('form-control') wire:model="description" rows="3" placeholder="Describe this reward..."></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category *</label>
                                <input type="text" @class('form-control') wire:model="category" placeholder="e.g., Performance, Attendance">
                                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Type *</label>
                                <select @class('form-select') wire:model="type">
                                    <option value="monetary">Monetary</option>
                                    <option value="non_monetary">Non-Monetary</option>
                                    <option value="recognition">Recognition</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Value ($)</label>
                                <input type="number" @class('form-control') wire:model="value" step="0.01" min="0" placeholder="0.00">
                                @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Points Required</label>
                                <input type="number" @class('form-control') wire:model="pointsRequired" min="0" placeholder="0">
                                @error('pointsRequired') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Icon Class</label>
                                <input type="text" @class('form-control') wire:model="icon" placeholder="e.g., bi bi-trophy-fill">
                                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="isActive">
                                    <label class="form-check-label">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Reward
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
