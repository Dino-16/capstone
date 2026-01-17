@section('page-title', 'Applications')
@section('page-subtitle', 'Manage job applications')
@section('breadcrumbs', 'Applications')

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
                    placeholder="Search..."
                />
            </div>
        </div>
    </div>
    {{-- MAIN TABLE --}}
    @if($applications)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>All Applications</h3>
            <p @class('text-secondary mb-0')>
                Overview of job applications
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Phone</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr wire:key="{{ $app->id }}">
                            <td>{{ $app->first_name }} {{ $app->middle_name }} {{ $app->last_name }} {{ $app->suffix_name }}</td>
                            <td>{{ $app->email }}</td>
                            <td>{{ $app->phone }}</td>
                            <td>{{ $app->applied_position }}</td>
                            <td>{{ $app->department }}</td>
                            <td>---</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                <div class="mt-3">No applications found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $applications->links() }}
        </div>
    @elseif($showDrafts)
            <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>Drafted Applications</h3>
            <p @class('text-secondary mb-0')>
                Only drafted applications
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Phone</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drafts as $draft)
                        @if($draft->status === 'Drafted')
                        <tr>
                            <td>{{ $draft->first_name }} {{ $draft->middle_name }} {{ $draft->last_name }} {{ $draft->suffix_name }}</td>
                            <td>{{ $draft->email }}</td>
                            <td>{{ $draft->phone }}</td>
                            <td>{{ $draft->applied_position }}</td>
                            <td>{{ $draft->department }}</td>
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
