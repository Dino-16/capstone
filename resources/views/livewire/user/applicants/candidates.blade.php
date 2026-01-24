@section('page-title', 'Candidates')
@section('page-subtitle', 'Manage scheduled candidates')
@section('breadcrumbs', 'Candidates')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-text-input
                    type="search"
                    wire:model.live="search" 
                    placeholder="Search candidates..."
                />
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    @if($candidates)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>All Candidates</h3>
            <p @class('text-secondary mb-0')>
                Overview of scheduled candidates
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Phone</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Interview Schedule</th>
                        <th @class('text-secondary')>Skills</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                        <tr wire:key="{{ $candidate->id }}">
                            <td>{{ $candidate->candidate_name }}</td>
                            <td>{{ $candidate->candidate_email }}</td>
                            <td>{{ $candidate->candidate_phone }}</td>
                            <td>
                                <span class="badge bg-{{ $candidate->status == 'scheduled' ? 'warning' : ($candidate->status == 'completed' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($candidate->status) }}
                                </span>
                            </td>
                            <td>
                                @if($candidate->interview_schedule)
                                    <span class="text-muted">
                                        {{ $candidate->interview_schedule->format('M d, Y') }} at {{ $candidate->interview_schedule->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </td>
                            <td>
                                @if($candidate->skills && is_array($candidate->skills) && count($candidate->skills) > 0)
                                    <span class="badge bg-info">{{ count($candidate->skills) }} skills</span>
                                @else
                                    <span class="text-muted">No skills</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button
                                        type="button"
                                        @class('btn btn-info btn-sm')
                                        wire:click="viewCandidate({{ $candidate->id }})"
                                        title="View Details"
                                    >
                                        <i @class('bi bi-eye-fill')></i>
                                    </button>
                                    <button
                                        type="button"
                                        @class('btn btn-warning btn-sm')
                                        wire:click="editCandidate({{ $candidate->id }})"
                                        title="Edit Candidate"
                                    >
                                        <i @class('bi bi-pencil-square')></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                <i @class('bi bi-person-x d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $candidates->links() }}
        </div>
    @endif

</div>
