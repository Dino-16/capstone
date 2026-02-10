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
                <x-search-input
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
                    Filter: {{ $statusFilter ? ucfirst(str_replace('_', ' ', $statusFilter)) : 'All' }}
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

        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                <button
                    @class('btn btn-success')
                    wire:click="export"
                >
                    Export
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
                    <th @class('text-secondary')>Description</th>
                    <th @class('text-secondary')>Type</th>
                    <th @class('text-secondary')>Benefits</th>
                    <th @class('text-secondary')>Status</th>
                    <th @class('text-secondary')>Actions</th>
                </tr>
            </thead>
                <tbody>
                    @forelse($rewards as $reward)
                        <tr wire:key="reward-{{ $reward->id }}">
                            <td>
                                <strong>{{ $reward->name }}</strong>
                            </td>
                            <td>
                                {{ Str::limit($reward->description, 30)}}
                            </td>
                            <td>{!! $reward->type_badge !!}</td>
                            <td>{{ $reward->benefits }}</td>
                            <td>{!! $reward->status_badge !!}</td>
                            <td>
                                <div @class('d-flex gap-2 align-items-center')>
                                    @if($reward->status === 'active')
                                        @if(session('user.position') === 'HR Manager')
                                        <button
                                            @class('btn btn-sm btn-outline-primary')
                                            wire:click="editReward({{ $reward->id }})"
                                            title="Edit"
                                            >
                                            <i @class('bi bi-pencil')></i>
                                        </button>
                                        @endif
                                        <button
                                            @class('btn btn-sm btn-outline-danger')
                                            wire:click="draft({{ $reward->id }})"
                                            title="Move to Draft"
                                        >
                                            <i @class('bi bi-journal-text')></i>
                                        </button>
                                        @if(in_array(session('user.position'), ['Super Admin', 'HR Manager']))
                                        <button
                                            @class('btn btn-sm btn-danger')
                                            wire:click="confirmDelete({{ $reward->id }})"
                                            title="Delete"
                                        >
                                            <i @class('bi bi-trash')></i>
                                        </button>
                                        @endif
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                @if($search)
                                    <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No rewards found matching "{{ $search }}".</div>
                                @elseif($statusFilter)
                                    <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No {{ str_replace('_', ' ', $statusFilter) }} rewards found.</div>
                                @else
                                    <i @class('bi bi-gift d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No rewards found.</div>
                                @endif
                            </td>
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
                        <th @class('text-secondary')>Description</th>
                        <th @class('text-secondary')>Type</th>
                        <th @class('text-secondary')>Benefits</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drafts as $draft)
                        <tr wire:key="draft-{{ $draft->id }}">
                            <td>
                                <strong>{{ $draft->name }}</strong>
                            </td>
                            <td>
                                {{ Str::limit($draft->description, 30)}}
                            </td>
                            <td>{!! $draft->type_badge !!}</td>
                            <td>{{ $draft->benefits }}</td>
                            <td>{!! $draft->status_badge !!}</td>
                            <td>
                                <div @class('d-flex gap-2 align-items-center')>
                                    <button
                                        @class('btn btn-sm btn-outline-warning')
                                        wire:click="restore({{ $draft->id }})"
                                        title="Restore Draft"
                                    >
                                        <i @class('bi bi-bootstrap-reboot')></i>
                                    </button>
                                    @if(in_array(session('user.position'), ['Super Admin', 'HR Manager']))
                                    <button
                                        @class('btn btn-sm btn-danger')
                                        wire:click="confirmDelete({{ $draft->id }})"
                                        title="Delete"
                                    >
                                        <i @class('bi bi-trash')></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" @class('text-center text-muted py-5')>
                                <i @class('bi bi-gift d-block mx-auto fs-1')></i>
                                <p @class('text-muted mb-0 mt-3')>No drafts found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $drafts->links() }}
        </div>
    @endif
    
    {{-- ELIGIBILITY TABLE --}}
    @if(!$showDrafts)
        <div @class('mt-5 p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <div @class('d-flex justify-content-between align-items-center')>
                <div>
                    <h3 @class('mb-0 text-primary')><i class="bi bi-stars me-2"></i>Reward Eligibility</h3>
                    <p @class('text-secondary mb-0')>
                        Employees matching key recognition criteria for <strong>{{ now()->format('F Y') }}</strong>
                    </p>
                </div>
                <button wire:click="loadEligibleEmployees" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-clockwise me-1" wire:loading.class="spin"></i> Refresh Criteria
                </button>
            </div>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table align-middle')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Employee</th>
                        <th @class('text-secondary')>Position/Dept</th>
                        <th @class('text-secondary')>Recognition Highlights</th>
                        <th @class('text-secondary text-end px-4')>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($loadingEligible)
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Analyzing HR data from internal systems...</p>
                            </td>
                        </tr>
                    @else
                        @forelse($eligibleEmployees as $emp)
                            <tr wire:key="eligible-{{ $emp['id'] }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">
                                            {{ $emp['avatar'] }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $emp['name'] }}</div>
                                            <div class="small text-muted">ID: #{{ substr($emp['id'], -6) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $emp['position'] }}</div>
                                    <div class="small text-muted">{{ $emp['department'] }}</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($emp['reasons'] as $reason)
                                            <span class="badge rounded-pill bg-white border text-dark d-flex align-items-center px-2 py-1 shadow-sm" style="font-weight: 500;">
                                                <i class="bi {{ $reason['icon'] }} me-2 text-primary" style="font-size: 1rem;"></i>
                                                {{ $reason['detail'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-end px-4">
                                    <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="alert('In a real scenario, this would open a Reward Granting form for {{ addslashes($emp['name']) }}')">
                                        <i class="bi bi-gift-fill me-1"></i> Give Reward
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="bg-light d-inline-block rounded-circle p-4 mb-3">
                                        <i class="bi bi-search fs-1"></i>
                                    </div>
                                    <p class="mb-0">No employees meet the automatic recognition criteria for this period.</p>
                                    <small>Checked: Performance (Rating 4+), Monthly Birthdays, and Work Anniversaries.</small>
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    @endif

    {{-- Modals --}}
    @include('livewire.user.recognition.includes.reward-delete-modal')
    @include('livewire.user.recognition.includes.reward-edit-modal')
</div>
