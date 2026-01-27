@section('page-title', 'Give Rewards')
@section('page-subtitle', 'Manage and distribute employee rewards')
@section('breadcrumbs', 'Give Rewards')

<div @class('pt-2')>

    {{-- Toast --}}
    <x-toast />

    @include('livewire.user.recognition.includes.give-rewards-card')

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
                    Filter: {{ $statusFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="filterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">
                            All Statuses
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'pending')">
                            Pending
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'approved')">
                            Approved
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'rejected')">
                            Rejected
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            {{-- GIVE REWARD BUTTON --}}
            <button
                @class('btn btn-primary')
                wire:click="openModal"
            >
                <i @class('bi bi-heart-fill me-2')></i>
                Give Reward
            </button>
            <button
                @class('btn btn-success')
                wire:click="export"
            >
                Export to Excel
            </button>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <h3 @class('mb-0')>Give Rewards Management</h3>
        <p @class('text-secondary mb-0')>
            Overview of rewards given to employees
        </p>
    </div>
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        <table @class('table')>
            <thead>
            <tr @class('bg-dark')>
                <th @class('text-secondary')>Employee</th>
                <th @class('text-secondary')>Reward</th>
                <th @class('text-secondary')>Given By</th>
                <th @class('text-secondary')>Date</th>
                <th @class('text-secondary')>Status</th>
                <th @class('text-secondary')>Actions</th>
            </tr>
        </thead>
            <tbody>
                @forelse($rewardsGiven as $rewardGiven)
                    <tr wire:key="reward-given-{{ $rewardGiven->id }}">
                        <td>
                            <div @class('d-flex align-items-center')>
                                <div @class('rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2') style="width: 32px; height: 32px;">
                                    <i @class('bi bi-person-fill text-primary')></i>
                                </div>
                                <div>
                                    <strong>{{ $rewardGiven->employee_name }}</strong>
                                    <br><small @class('text-muted')>{{ $rewardGiven->employee_email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($rewardGiven->reward)
                                <div @class('d-flex align-items-center')>
                                    <div @class('rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center me-2') style="width: 32px; height: 32px;">
                                        <i @class('bi bi-gift text-info')></i>
                                    </div>
                                    <div>
                                        <strong>{{ $rewardGiven->reward->name }}</strong>
                                        <br><small @class('text-muted')>{{ Str::limit($rewardGiven->reward->description, 50) }}</small>
                                        <br><small @class('text-muted')>{{ $rewardGiven->reward->type }}</small>
                                    </div>
                                </div>
                            @else
                                <span @class('text-muted')>Reward not found</span>
                            @endif
                        </td>
                        <td>
                            <div @class('d-flex align-items-center')>
                                <div @class('rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center me-2') style="width: 32px; height: 32px;">
                                    <i @class('bi bi-person-badge-fill text-success')></i>
                                </div>
                                {{ $rewardGiven->given_by }}
                            </div>
                        </td>
                        <td>
                            <div @class('d-flex align-items-center')>
                                <i @class('bi bi-calendar3 me-2 text-muted')></i>
                                {{ $rewardGiven->given_date->format('M d, Y') }}
                            </div>
                        </td>
                        <td>{!! $rewardGiven->status_badge !!}</td>
                        <td @class('gap-3')>
                            <button
                                @class('btn btn-primary btn-sm me-2')
                                wire:click="editRewardGiven({{ $rewardGiven->id }})"
                                title="Edit"
                            >
                                <i @class('bi bi-pencil')></i>
                            </button>
                            <button
                                @class('btn btn-danger btn-sm')
                                wire:click="deleteRewardGiven({{ $rewardGiven->id }})"
                                wire:confirm="Are you sure you want to delete this reward given?"
                                title="Delete"
                            >
                                <i @class('bi bi-trash')></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" @class('text-center text-muted py-5')>
                            <i @class('bi bi-heart text-muted fs-1')></i>
                            <p @class('text-muted mt-3 mb-0')>No rewards given yet</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($rewardsGiven->hasPages())
        <div @class('d-flex justify-content-center mt-4')>
            {{ $rewardsGiven->links() }}
        </div>
    @endif

    @include('livewire.user.recognition.includes.give-rewards-modal')

    <!-- Success Message -->
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>
