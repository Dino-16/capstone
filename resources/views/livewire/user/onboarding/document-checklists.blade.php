<div>
    @section('page-title', 'Document Checklists')
    @section('page-subtitle', 'Manage employee documents')
    @section('breadcrumbs', 'Document Checklists')

<div @class('pt-2')>

    {{-- Toast --}}
    <x-toast />

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
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                {{-- ADD EMPLOYEE BUTTON --}}
                <button
                    @class('btn btn-primary')
                    wire:click="openModal"
                >
                Add Checklist
                </button>
                <button
                    @class('btn btn-success')
                    wire:click="export"
                >
                    Export to Excel
                </button>

                {{-- DRAFT BUTTON --}}
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
    @if(!$showDrafts)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3 @class('mb-0')>Employee Document Checklists</h3>
            <p @class('text-secondary mb-0')>
                Overview of employee document requirements
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary')>Employee Name</th>
                    <th @class('text-secondary')>Email</th>
                    <th @class('text-secondary')>Documents</th>
                    <th @class('text-secondary')>Completion</th>
                    <th @class('text-secondary')>Actions</th>
                </tr>
            </thead>
                <tbody>
                    @forelse($documentChecklists as $document)
                        <tr wire:key="emp-{{ $document->id }}">
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div>
                                        <strong>{{ $document->employee_name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $document->email }}
                            </td>
                            <td>
                                <div @class('d-flex flex-wrap gap-1')>
                                    @if($document->documents && is_array($document->documents))
                                        @foreach($document->documents as $docType => $status)
                                            <span @class('badge bg-primary text-white')>
                                                {{ ucwords(str_replace('_', ' ', $docType)) }} 
                                            </span>
                                            <br>
                                        @endforeach
                                    @else
                                        <span @class('text-muted')>No documents</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div @class('d-flex flex-column gap-1')>
                                    {{-- Completion Badge --}}
                                    @if($document->getCompletionPercentage() == 100)
                                        <span @class('badge bg-primary text-white')>Complete</span>
                                    @else
                                        <span @class('badge bg-warning text-white')>Incomplete</span>
                                    @endif
                                </div>
                                <small @class('text-muted d-block mt-1')>
                                    Docs: {{ count($document->documents ?? []) }}/6 | 
                                    %: {{ number_format($document->getCompletionPercentage(), 0) }}%
                                </small>
                            </td>
                            <td>
                                <div @class('d-flex gap-2 align-items-center')>
                                    <button
                                        @class('btn btn-primary btn-sm')
                                        wire:click="editEmployee({{ $document->id }})"
                                        title="Edit"
                                    >
                                        <i @class('bi bi-pencil')></i>
                                    </button>
                                    <button
                                        @class('btn btn-info btn-sm')
                                        wire:click="openMessageModal({{ $document->id }})"
                                        title="Message"
                                    >
                                        <i @class('bi bi-envelope')></i>
                                    </button>
                                    <button
                                        @class('btn btn-danger btn-sm')
                                        wire:click="draft({{ $document->id }})"
                                        title="Draft Employee"
                                    >
                                        <i @class('bi bi-journal-text')></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" @class('text-center text-muted')>No employees found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        {{-- DRAFT TABLE --}}
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3 @class('mb-0')>Draft Employees</h3>
            <p @class('text-secondary mb-0')>
                Only draft employees
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary')>Employee Name</th>
                    <th @class('text-secondary')>Email</th>
                    <th @class('text-secondary')>Documents</th>
                    <th @class('text-secondary')>Completion</th>
                    <th @class('text-secondary')>Action</th>
                </tr>
            </thead>
                <tbody>
                    @forelse($drafts as $draft)
                        <tr>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div>
                                        <strong>{{ $draft->employee_name }}</strong>
                                        @if($draft->notes)
                                            <br><small @class('text-muted')>{{ $draft->notes }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($draft->email)
                                    <a href="mailto:{{ $draft->email }}" @class('text-decoration-none')>
                                        <i @class('bi bi-envelope me-1')></i>{{ $draft->email }}
                                    </a>
                                @else
                                    <span @class('text-muted')>No email</span>
                                @endif
                            </td>
                            <td>
                                <div @class('d-flex flex-wrap gap-1')>
                                    @if($draft->documents && is_array($draft->documents))
                                        @foreach($draft->documents as $docType => $status)
                                            <span @class('badge bg-primary text-white')>
                                                {{ ucwords(str_replace('_', ' ', $docType)) }} 
                                            </span>
                                            <br>
                                        @endforeach
                                    @else
                                        <span @class('text-muted')>No documents</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div @class('d-flex flex-column gap-1')>
                                    {{-- Completion Badge --}}
                                    @if($draft->getCompletionPercentage() == 100)
                                        <span @class('badge bg-primary text-white')>Complete</span>
                                    @else
                                        <span @class('badge bg-warning text-white')>Incomplete</span>
                                    @endif
                                </div>
                                <small @class('text-muted d-block mt-1')>
                                    Docs: {{ count($draft->documents ?? []) }}/6 | 
                                    %: {{ number_format($draft->getCompletionPercentage(), 0) }}%
                                </small>
                            </td>
                            <td>
                                <button
                                    @class('btn btn-primary btn-sm')
                                    wire:click="restore({{ $draft->id }})"
                                    title="Restore Draft"
                                >
                                    <i @class('bi bi-bootstrap-reboot')></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" @class('text-center text-muted')>No drafts found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $drafts->links() }}
        </div>
    @endif

    {{-- Edit Employee Modal --}}
    @include('livewire.user.onboarding.includes.document-edit')

    {{-- Add Employee Modal --}}
    @include('livewire.user.onboarding.includes.document-add')

    {{-- Message Employee Modal --}}
    @include('livewire.user.onboarding.includes.document-message')

</div>
</div>