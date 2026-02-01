@section('page-title', 'Job Posting')
@section('page-subtitle', 'Manage job post')
@section('breadcrumbs', 'job posting')

<div @class('pt-2')>

    {{-- TOAST --}}
    <x-toast />


    <div @class('row')>
        <div @class('col-md-8')>
            {{-- HEADER ACTIONS --}}
            <div @class('d-flex justify-content-between align-items-center mb-3')>

                {{-- SEARCH BAR --}}
                <div>
                    <x-text-input
                        type="search"
                        wire:model.live.debounce.3s="search"
                        placeholder="Search..."
                    />
                </div>

                <div >
                    <button
                        @class('btn btn-success')
                        wire:click="export"
                    >
                        Export to Excel
                    </button>
                </div>
            </div>

            <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
                <h3>All Posted Jobs</h3>
                <p @class('text-secondary mb-0')>
                    Overview of posted jobs
                </p>
            </div>
            <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0 mb-3')>
                <table @class('table')>
                    <thead>
                        <tr @class('bg-dark')>
                            <th @class('text-secondary')>Position</th>
                            <th @class('text-secondary')>Department</th>
                            <th @class('text-secondary')>Location</th>
                            <th @class('text-secondary')>Posted Date</th>
                            <th @class('text-secondary')>Expiration Date</th>
                            <th @class('text-secondary')>Status</th>
                            <th @class('text-secondary')>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr wire:key="{{ $job->id }}">
                                <td><strong>{{ $job->position }}</strong></td>
                                <td>{{ $job->department ?? 'N/A' }}</td>
                                <td>{{ $job->location ?? 'Ever Gotesco Commonwealth' }}</td>
                                <td>{{ $job->created_at ? $job->created_at->format('M d, Y') : '—' }}</td>
                                <td>{{ $job->expiration_date ? \Carbon\Carbon::parse($job->expiration_date)->format('M d, Y') : '—' }}</td>
                                <td><span class="badge bg-success">{{ $job->status }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button wire:click="editJob({{ $job->id }})" 
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button wire:click="deactivateJob({{ $job->id }})" 
                                            class="btn btn-sm btn-outline-danger"
                                            title="Remove"
                                            onclick="return confirm('Are you sure you want to remove this job?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-briefcase d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No Active Jobs.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $jobs->links() }}
            </div>
        </div>
        <div @class('col-md-4')>
            <div @class('d-grid gap-3')>
                <!-- Accepted Requisitions -->
                <div @class('bg-white rounded border')>
                    <div @class('p-3 border-bottom card-header')>
                        <div>
                            <h5 @class('mb-1 fw-bold')>Accepted Requisitions</h5>
                            <small @class('text-muted')>{{ $requisitions->count() }} requisitions ready for posting</small>
                        </div>
                    </div>
                    <div @class('p-3') style='max-height: 300px; overflow-y: auto;'>
                        @forelse($requisitions as $req)
                            <div @class('d-flex justify-content-between align-items-center p-2 rounded hover-bg-light cursor-pointer') wire:click="createJobFromRequisition({{ $req->id }})">
                                <p @class('mb-0 fw-semibold text-truncate')>{{ $req->position }}</p>
                                <p @class('mb-0 text-muted small')>{{ $req->department }}</p>
                            </div>
                        @empty
                            <div @class('text-center py-4')>
                                <div @class('bg-light bg-opacity-50 rounded-circle p-3 mx-auto mb-2') style='width: 48px; height: 48px;'>
                                    <i @class('bi bi-clipboard-x text-muted')></i>
                                </div>
                                <p @class('text-muted mb-0 small')>No accepted requisitions</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Lists of Jobs from API -->
                <div @class('bg-white rounded border')>
                    <div @class('p-3 border-bottom')>
                        <div @class('d-flex justify-content-between align-items-start')>
                            <div>
                                <h5 @class('mb-1 fw-bold')>Lists of Jobs</h5>
                                <small @class('text-muted')>{{ count($apiPositions ?? []) }} jobs available</small>
                            </div>
                            <div @class('d-flex align-items-center gap-2')>
                                <div @class('input-group input-group-sm') style='width: 120px;'>
                                    <span @class('input-group-text')>
                                        <i @class('bi bi-sort-alpha-down')></i>
                                    </span>
                                    <select wire:model.live='jobListSort' @class('form-select form-select-sm')>
                                        <option value='position_asc'>A-Z</option>
                                        <option value='position_desc'>Z-A</option>
                                    </select>
                                </div>
                                <button wire:click='clearJobListFilter' @class('btn btn-sm btn-outline-secondary')>
                                    <i @class('bi bi-arrow-clockwise')></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div @class('p-3') style='max-height: 400px; overflow-y: auto;'>
                        @php
                            $positions = collect($apiPositions ?? []);
                            if ($jobListSort === 'position_desc') {
                                $positions = $positions->sortByDesc('position_name');
                            } else {
                                $positions = $positions->sortBy('position_name');
                            }
                            
                            // Get list of already activated positions
                            $activatedPositions = \App\Models\Recruitment\JobListing::where('status', 'Active')
                                ->pluck('position')
                                ->toArray();
                        @endphp
                        
                        @forelse($positions as $position)
                            @php
                                $isActivated = in_array($position['position_name'], $activatedPositions);
                            @endphp
                            <div @class('d-flex justify-content-between align-items-center p-2 rounded border-bottom')>
                                <div @class('flex-grow-1')>
                                    <p @class('mb-1 fw-semibold text-truncate')>{{ $position['position_name'] }}</p>
                                    <p @class('mb-0 text-muted small')>{{ $position['department'] ?? 'N/A' }}</p>
                                </div>
                                @if($isActivated)
                                    <button 
                                        @class('btn btn-sm btn-secondary ms-2')
                                        disabled
                                    >
                                        Activated
                                    </button>
                                @else
                                    @php
                                        $positionJson = json_encode($position);
                                    @endphp
                                    <button 
                                        @class('btn btn-sm btn-primary ms-2')
                                        wire:click="activateApiPosition({{ $positionJson }})"
                                        wire:loading.attr="disabled"
                                        wire:target="activateApiPosition({{ $positionJson }})"
                                    >
                                        <span wire:loading.remove wire:target="activateApiPosition({{ $positionJson }})">Activate</span>
                                        <span wire:loading wire:target="activateApiPosition({{ $positionJson }})" class="spinner-border spinner-border-sm"></span>
                                    </button>
                                @endif
                            </div>
                        @empty
                            <div @class('text-center py-4')>
                                <i @class('bi bi-briefcase fs-1 text-muted d-block mb-2')></i>
                                <p @class('text-muted mb-0 small')>No positions from API</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @include('livewire.user.recruitment.includes.job-details')
</div>