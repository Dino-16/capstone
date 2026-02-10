<div>
    @section('page-title', 'Document Checklists')
    @section('page-subtitle', 'Manage employee documents')
    @section('breadcrumbs', 'Document Checklists')

<div @class('pt-2')>

    {{-- PASSWORD GATE --}}
    @include('components.password-gate')

    {{-- Toast --}}
    <x-toast />

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

            {{-- Department Filter --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-building me-2')></i>
                    Department: {{ $departmentFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('departmentFilter', '')">All Departments</a>
                    </li>
                    @foreach($departments as $dept)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('departmentFilter', '{{ $dept }}')">{{ $dept }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Position Filter --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-person-badge me-2')></i>
                    Position: {{ $positionFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('positionFilter', '')">All Positions</a>
                    </li>
                    @foreach($positions as $pos)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('positionFilter', '{{ $pos }}')">{{ $pos }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- COMPLETION FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="completionFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Status: {{ $completionFilter }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="completionFilterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('completionFilter', 'All')">All</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('completionFilter', 'Complete')">Complete</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('completionFilter', 'Incomplete')">Incomplete</a>
                    </li>
                </ul>
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
                    Export
                </button>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
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
                <th @class('text-secondary')>Employee</th>
                <th @class('text-secondary')>Position</th>
                <th @class('text-secondary')>Department</th>
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
                                <div @class('rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2') style="width: 32px; height: 32px;">
                                    <i @class('bi bi-person text-primary')></i>
                                </div>
                                <div>
                                    <strong>{{ $document->employee_name }}</strong>
                                    <br><small @class('text-muted')>{{ $document->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $document->position ?? '---' }}</td>
                        <td>{{ $document->department ?? '---' }}</td>
                        <td>
                            <div @class('d-flex align-items-center')>
                                <button 
                                    class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2 rounded-pill px-3 shadow-sm hover-elevate" 
                                    wire:click="viewChecklist({{ $document->id }})"
                                    title="View Documents"
                                >
                                    <i class="bi bi-eye"></i> 
                                    <span>View Documents</span>
                                </button>
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
                                Docs: {{ $document->getCompleteCount() }}/{{ $document->getTotalCount() }} | 
                                %: {{ number_format($document->getCompletionPercentage(), 0) }}%
                            </small>
                        </td>
                        <td>
                            <div @class('d-flex gap-2 align-items-center')>
                                <button
                                    @class('btn btn-sm btn-outline-primary')
                                    wire:click="editEmployee({{ $document->id }})"
                                    title="Edit"
                                >
                                    <i @class('bi bi-pencil')></i>
                                </button>
                                <button
                                    @class('btn btn-sm btn-outline-info')
                                    wire:click="openMessageModal({{ $document->id }})"
                                    title="Message"
                                >
                                    <i @class('bi bi-envelope')></i>
                                </button>
                                <button
                                    @class('btn btn-sm btn-outline-danger')
                                    wire:click="confirmDelete({{ $document->id }})"
                                    title="Delete"
                                >
                                    <i @class('bi bi-trash')></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    @if($search)
                        <tr>
                            <td colspan="5" @class('text-center text-muted py-5')>
                                <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                <div class="mt-3">No checklists found matching "{{ $search }}".</div>
                            </td>
                        </tr>
                    @elseif($completionFilter === 'Complete')
                        <tr>
                            <td colspan="5" @class('text-center text-muted py-5')>
                                <i @class('bi bi-check-circle d-block mx-auto fs-1')></i>
                                <div class="mt-3">No complete checklists found</div>
                            </td>
                        </tr>
                    @elseif($completionFilter === 'Incomplete')
                        <tr>
                            <td colspan="5" @class('text-center text-muted py-5')>
                                <i @class('bi bi-x d-block mx-auto fs-1')></i>
                                <div class="mt-3">No incomplete checklists found</div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5" @class('text-center text-muted py-5')>
                                <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                <div class="mt-3">No checklists found</div>
                            </td>
                        </tr>
                    @endif
                @endforelse
            </tbody>
        </table>
        {{ $documentChecklists->links() }}
    </div>

    {{-- View Documents Modal --}}
    @include('livewire.user.onboarding.includes.document-view')

    {{-- Edit Employee Modal --}}
    @include('livewire.user.onboarding.includes.document-edit')

    {{-- Add Employee Modal --}}
    @include('livewire.user.onboarding.includes.document-add')

    {{-- Message Employee Modal --}}
    @include('livewire.user.onboarding.includes.document-message')

    {{-- Delete Confirmation Modal --}}
    @include('livewire.user.onboarding.includes.document-delete')

</div>
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-document', (event) => {
            if (event.url) {
                window.open(event.url, '_blank');
            }
        });
    });
</script>