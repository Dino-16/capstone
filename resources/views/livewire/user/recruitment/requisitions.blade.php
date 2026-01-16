@section('page-title', 'Positions')
@section('page-subtitle', 'Manage job positions')
@section('breadcrumbs', 'Positions')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- STATUS CARDS --}}
    @include('livewire.user.recruitment.includes.requisition-cards')

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-text-input
                    type="search"
                    wire:model.live="search" 
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
                    Filter: {{ $statusFilter }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="filterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'All')">
                            All
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Pending')">
                           Pending
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Accepted')">
                            Accepted
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Drafted')">
                            Drafted
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
    @if($requisitions)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>All Positions</h3>
            <p @class('text-secondary mb-0')>
                Overview of positions
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
                                @elseif($req->status === 'Drafted')
                                    <span @class('badge bg-danger')>{{ $req->status }}</span>
                                @else
                                    <span @class('badge bg-secondary')>No Data</span>
                                @endif
                            </td>
                            @if($req->status === 'Pending')
                                <td @class('gap-3')>
                                    <button
                                        @class('btn btn-success btn-sm')
                                        wire:click="approve({{ $req->id }})"
                                        title="Approve"
                                    >
                                        <i @class('bi bi-check-lg')></i>
                                    </button>

                                    <button
                                        @class('btn btn-danger btn-sm')
                                        wire:click="draft({{ $req->id }})"
                                        title="Draft"
                                    >
                                        <i @class('bi bi-file-earmark-text')></i>
                                    </button>
                                </td>
                            @else
                                <td>---</td>
                            @endif
                        </tr>
                    @empty
                        @if($statusFilter === 'Pending')
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-hourglass-split d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No pending position found.</div>
                                </td>
                            </tr>
                        @elseif($statusFilter === 'Drafted')
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-file-earmark-text d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No drafted position found.</div>
                                </td>
                            </tr>
                        @elseif($statusFilter === 'Accepted')
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-check-circle d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No approved position found.</div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No position found.</div>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
            {{ $requisitions->links() }}
        </div>
    @elseif($showDrafts)
            <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>Draft position/s</h3>
            <p @class('text-secondary mb-0')>
                Only draft position/s
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
                        <th @class('text-secondary')>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drafts as $draft)
                        @if($draft->status === 'Drafted')
                        <tr>
                            <td>{{ $draft->requested_by }}</td>
                            <td>{{ $draft->department }}</td>
                            <td>{{ $draft->position }}</td>
                            <td>{{ $draft->opening }}</td>
                            <td><span @class('badge bg-danger')>{{ $draft->status }}</span></td>
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
                        @endif
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
</div>
