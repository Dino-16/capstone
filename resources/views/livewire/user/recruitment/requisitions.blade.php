@section('page-title', 'Recruitment Requests')
@section('page-subtitle', 'Overview of requisitions')
@section('breadcrumbs', 'Recruitment Requests')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />




    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-search-input
                    wire:model.live="search" 
                    placeholder="Search..."
                />
            </div>

            {{-- STATUS FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="statusFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-check-circle-fill me-2')></i>
                    Status: {{ $statusFilter }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="statusFilterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'All')">All</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Pending')">Pending</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Accepted')">Accepted</a>
                    </li>
                </ul>
            </div>

            {{-- DEPARTMENT FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="deptFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-building me-2')></i>
                    Department: {{ $departmentFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="deptFilterDropdown" style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('departmentFilter', '')">All Departments</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($departments as $dept)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('departmentFilter', '{{ $dept }}')">
                                {{ $dept }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- POSITION FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="posFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-person-workspace me-2')></i>
                    Position: {{ $positionFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="posFilterDropdown" style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('positionFilter', '')">All Positions</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($positions as $pos)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('positionFilter', '{{ $pos }}')">
                                {{ $pos }}
                            </a>
                        </li>
                    @endforeach
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
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    @if($requisitions)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>Recruitment Requests</h3>
            <p @class('text-secondary mb-0')>
                Overview of recruitment requests
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Requested by</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>Opening</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $req)
                        <tr wire:key="{{ $req->id }}">
                            <td>{{ $req->requested_by }}</td>
                            <td>{{ $req->department }}</td>
                            <td>{{ $req->position }}</td>
                            <td>{{ $req->opening }}</td>
                            <td>
                                @if($req->status === 'Accepted')
                                    <span @class('badge bg-success')>{{ $req->status }}</span>
                                @elseif($req->status === 'Pending')
                                    <span @class('badge bg-warning text-dark')>{{ $req->status }}</span>
                                @else
                                    <span @class('badge bg-secondary')>No Data</span>
                                @endif
                            </td>
                            <td>
                                @if($req->status === 'Accepted' && !in_array(session('user.position'), ['Super Admin', 'HR Manager']))
                                    <span class="text-muted">-----</span>
                                @else
                                    <div class="d-flex gap-2">
                                        <button
                                            @class('btn btn-sm btn-outline-primary')
                                            wire:click="viewRequisition({{ $req->id }})"
                                            title="View Details"
                                        >
                                            <i @class('bi bi-eye')></i>
                                        </button>

                                        @if($req->status === 'Pending')
                                            @if(session('user.position') === 'HR Manager')
                                                <button
                                                    class="btn btn-sm btn-outline-primary"
                                                    wire:click="editRequisition({{ $req->id }})"
                                                    title="Edit"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            @endif
                                            <button
                                                @class('btn btn-sm btn-success')
                                                wire:click="approve({{ $req->id }})"
                                                title="Approve"
                                            >
                                                <i @class('bi bi-check-lg')></i>
                                            </button>
                                            
                                            @if(in_array(session('user.position'), ['Super Admin', 'HR Manager']))
                                                <button
                                                    @class('btn btn-sm btn-danger')
                                                    wire:click="confirmDelete({{ $req->id }})"
                                                    title="Delete"
                                                >
                                                    <i @class('bi bi-trash')></i>
                                                </button>
                                            @endif
                                        @elseif($req->status === 'Accepted' && in_array(session('user.position'), ['Super Admin', 'HR Manager']))
                                            <button
                                                @class('btn btn-sm btn-danger')
                                                wire:click="confirmDelete({{ $req->id }})"
                                                title="Delete"
                                            >
                                                <i @class('bi bi-trash')></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        @if($search)
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No recruitment requests found matching "{{ $search }}".</div>
                                </td>
                            </tr>
                        @elseif($statusFilter === 'Pending')
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-hourglass-split d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No pending recruitment requests found.</div>
                                </td>
                            </tr>
                        @elseif($statusFilter === 'Accepted')
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-check-circle d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No accepted recruitment requests found.</div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No recruitment requests found.</div>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
            {{ $requisitions->links() }}
        </div>
    @endif

    {{-- Modals --}}
    @include('livewire.user.recruitment.includes.requisition-delete-modal')
    @include('livewire.user.recruitment.includes.requisition-view-modal')
    @include('livewire.user.recruitment.includes.requisition-edit-modal')
</div>

