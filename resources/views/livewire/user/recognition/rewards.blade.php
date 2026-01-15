@section('page-title', 'Rewards Management')
@section('page-subtitle', 'Manage employee rewards and recognition')
@section('breadcrumbs', 'Rewards')

<div @class('pt-2')>

    {{-- Toast --}}
    <x-toast />

    {{-- STATUS CARDS --}}
    @include('livewire.user.recognition.includes.rewards-cards')

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-text-input
                    type="search"
                    wire:model.live.debounce.3s="search"
                    placeholder="Search..."
                />
            </div>

            {{-- FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="filterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: All
                </button>

                <ul @class('dropdown-menu') aria-labelledby="filterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">
                            All Types
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'monetary')">
                            Monetary
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'non_monetary')">
                            Non-Monetary
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'recognition')">
                            Recognition
                        </a>
                    </li>
                </ul>
            </div>

            {{-- CREATE REWARD BUTTON --}}
            <button
                @class('btn btn-primary')
                wire:click="openModal"
            >
                <i @class('bi bi-plus-circle me-2')></i>
                Create Reward
            </button>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                <button
                    @class('btn btn-success')
                    wire:click="export"
                >
                    Export to Excel
                </button>

                @if(!$showDrafts)
                    <button
                        @class('btn btn-danger')
                        wire:click="openDraft"
                    >
                        Open Drafts
                    </button>
                @else
                    <button
                        @class('btn btn-danger')
                        wire:click="openDraft"
                        disabled
                    >
                        Open Drafts
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($showDrafts)
        <div @class('mb-3')>
            <button @class('btn btn-default') wire:click="showAll"><i class="bi bi-arrow-left-circle-fill me-1"></i>Back to All</button>
        </div>
    @endif

    {{-- MAIN TABLE --}}
    @if($rewards !== null)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3 @class('mb-0')>Rewards Management</h3>
            <p @class('text-secondary mb-0')>
                Manage employee rewards and recognition
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary')>Reward Name</th>
                    <th @class('text-secondary')>Category</th>
                    <th @class('text-secondary')>Type</th>
                    <th @class('text-secondary')>Value</th>
                    <th @class('text-secondary')>Points</th>
                    <th @class('text-secondary')>Status</th>
                    <th @class('text-secondary')>Actions</th>
                </tr>
            </thead>
                <tbody>
                    @forelse($rewards as $reward)
                        <tr wire:key="reward-{{ $reward->id }}">
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div @class('rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2') style="width: 32px; height: 32px;">
                                        @if($reward->icon)
                                            <i @class('{{ $reward->icon }} text-primary')></i>
                                        @else
                                            <i @class('bi bi-gift text-primary')></i>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $reward->name }}</strong>
                                        <br><small @class('text-muted')>{{ Str::limit($reward->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span @class('badge bg-light text-dark')>{{ $reward->category }}</span>
                            </td>
                            <td>{!! $reward->type_badge !!}</td>
                            <td>
                                @if($reward->value > 0)
                                    ${{ number_format($reward->value, 2) }}
                                @else
                                    <span @class('text-muted')>---</span>
                                @endif
                            </td>
                            <td>
                                @if($reward->points_required > 0)
                                    {{ $reward->points_required }}
                                @else
                                    <span @class('text-muted')>---</span>
                                @endif
                            </td>
                            <td>{!! $reward->status_badge !!}</td>
                            <td>
                                @if($reward->status === 'active')
                                    <button
                                        @class('btn btn-danger btn-sm')
                                        wire:click="draft({{ $reward->id }})"
                                        title="Draft"
                                    >
                                        <i @class('bi bi-journal-text')></i>
                                    </button>
                                @else
                                    <span>---</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted')>No rewards found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rewards->hasPages())
            <div @class('d-flex justify-content-center mt-4')>
                {{ $rewards->links() }}
            </div>
        @endif

    {{-- DRAFT TABLE --}}
    @elseif($drafts !== null)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>Draft Rewards</h3>
            <p @class('text-secondary mb-0')>
                Only draft rewards
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Reward Name</th>
                        <th @class('text-secondary')>Category</th>
                        <th @class('text-secondary')>Type</th>
                        <th @class('text-secondary')>Value</th>
                        <th @class('text-secondary')>Points</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drafts as $draft)
                        <tr wire:key="draft-{{ $draft->id }}">
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div @class('rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2') style="width: 32px; height: 32px;">
                                        @if($draft->icon)
                                            <i @class('{{ $draft->icon }} text-primary')></i>
                                        @else
                                            <i @class('bi bi-gift text-primary')></i>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $draft->name }}</strong>
                                        <br><small @class('text-muted')>{{ Str::limit($draft->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span @class('badge bg-light text-dark')>{{ $draft->category }}</span>
                            </td>
                            <td>{!! $draft->type_badge !!}</td>
                            <td>
                                @if($draft->value > 0)
                                    ${{ number_format($draft->value, 2) }}
                                @else
                                    <span @class('text-muted')>---</span>
                                @endif
                            </td>
                            <td>
                                @if($draft->points_required > 0)
                                    {{ $draft->points_required }}
                                @else
                                    <span @class('text-muted')>---</span>
                                @endif
                            </td>
                            <td>{!! $draft->status_badge !!}</td>
                            <td>
                                <button
                                    @class('btn btn-primary btn-sm')
                                    wire:click="restore({{ $draft->id }})"
                                    title="Restore"
                                >
                                    <i @class('bi bi-bootstrap-reboot')></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted')>
                                No drafts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $drafts->links() }}
        </div>
    @endif

    <!-- Add/Edit Reward Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title">{{ $editing ? 'Edit Reward' : 'Create New Reward' }}</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('showModal', false)"></button>
                    </div>

                    <form wire:submit="{{ $editing ? 'updateReward' : 'addReward' }}">
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
                                <i class="bi bi-check-circle me-2"></i>{{ $editing ? 'Update Reward' : 'Create Reward' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
