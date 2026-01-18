@section('page-title', 'Give Rewards')
@section('page-subtitle', 'Manage and distribute employee rewards')
@section('breadcrumbs', 'Give Rewards')

<div @class('pt-2')>

    {{-- Toast --}}
    <x-toast />

    {{-- STATUS CARDS --}}
    <div @class('row g-3 mb-3')>
        <div @class('col-md-3')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                {{-- Icon --}}
                <div @class('mb-2')>
                    <i @class('bi bi-gift text-primary fs-3')></i>
                </div>

                <div @class('ps-2')>
                    {{-- Count --}}
                    <div @class('fw-semi fs-4')>
                        {{ $rewardsGiven->total() }}
                    </div>

                    {{-- Label --}}
                    <div @class('text-muted small')>
                        Total Given
                    </div>
                </div>
            </div>
        </div>
        <div @class('col-md-3')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                {{-- Icon --}}
                <div @class('mb-2')>
                    <i @class('bi bi-hourglass-split text-warning fs-3')></i>
                </div>

                <div @class('ps-2')>
                    {{-- Count --}}
                    <div @class('fw-semi fs-4')>
                        {{ $rewardsGiven->where('status', 'pending')->count() }}
                    </div>

                    {{-- Label --}}
                    <div @class('text-muted small')>
                        Pending
                    </div>
                </div>
            </div>
        </div>
        <div @class('col-md-3')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                {{-- Icon --}}
                <div @class('mb-2')>
                    <i @class('bi bi-check-circle-fill text-success fs-3')></i>
                </div>

                <div @class('ps-2')>
                    {{-- Count --}}
                    <div @class('fw-semi fs-4')>
                        {{ $rewardsGiven->where('status', 'approved')->count() }}
                    </div>

                    {{-- Label --}}
                    <div @class('text-muted small')>
                        Approved
                    </div>
                </div>
            </div>
        </div>
        <div @class('col-md-3')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                {{-- Icon --}}
                <div @class('mb-2')>
                    <i @class('bi bi-star-fill text-dark fs-3')></i>
                </div>

                <div @class('ps-2')>
                    {{-- Count --}}
                    <div @class('fw-semi fs-4')>
                        {{ $rewards->count() }}
                    </div>

                    {{-- Label --}}
                    <div @class('text-muted small')>
                        Available Rewards
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                <span @class('text-muted')">Reward not found</span>
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

    <!-- Add/Edit Reward Given Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">{{ $editing ? 'Edit Reward Given' : 'Give Reward to Employee' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>

                    <form wire:submit.prevent="{{ $editing ? 'updateRewardGiving' : 'addRewardGiving' }}">
                        <div class="modal-body p-4">
                            <!-- Reward Selection -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Select Reward *</label>
                                    <select class="form-select @error('rewardId') is-invalid @enderror" wire:model="rewardId">
                                        <option value="">Choose a reward...</option>
                                        @foreach($rewards as $reward)
                                            <option value="{{ $reward->id }}">{{ $reward->name }} - {{ $reward->type }}</option>
                                        @endforeach
                                    </select>
                                    @error('rewardId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Given Date *</label>
                                    <input type="date" class="form-control @error('givenDate') is-invalid @enderror" wire:model="givenDate">
                                    @error('givenDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Employee Information -->
                            <div @class('mb-4 position-relative')>
                            <label @class('form-label fw-bold')>Search Employee</label>
                            <input 
                                type="text" 
                                @class('form-control') 
                                wire:model.live="employeeName" 
                                placeholder="Type to search employees..."
                                autocomplete="off"
                            >
                            @error('employeeName') <div @class('invalid-feedback d-block')>{{ $message }}</div> @enderror
                            
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
                                            @if(isset($employee['email']))
                                                <br><small @class('text-muted')>{{ $employee['email'] }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($showEmployeeDropdown && count($filteredEmployees) == 0)
                                <div @class('position-absolute w-100 bg-white border rounded shadow-lg mt-1') style="z-index: 1000;">
                                    <div @class('px-3 py-4 text-center text-muted')>
                                        <i @class('bi bi-person-x fs-3 d-block mx-auto mb-2')></i>
                                        Employee not found
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Employee Email *</label>
                                <input type="email" class="form-control @error('employeeEmail') is-invalid @enderror" wire:model="employeeEmail" placeholder="Auto-populated when employee is selected" readonly>
                                @error('employeeEmail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Position</label>
                                <input type="text" class="form-control @error('employeePosition') is-invalid @enderror" wire:model="employeePosition" placeholder="Job position">
                                @error('employeePosition') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                            <!-- Giver Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Given By *</label>
                                    <input type="text" class="form-control @error('givenBy') is-invalid @enderror" wire:model="givenBy" placeholder="Your name">
                                    @error('givenBy') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Additional Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" wire:model="notes" rows="2" placeholder="Any additional comments..."></textarea>
                                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div @class('modal-footer')>
                            <button type="button" @class('btn btn-secondary') wire:click="$set('showModal', false)">Cancel</button>
                            <button type="submit" @class('btn btn-primary')>
                                <i @class('bi bi-check-circle me-2')></i>{{ $editing ? 'Update Reward' : 'Give Reward' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Message -->
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>
