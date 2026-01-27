<div class="pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="w-25">
            <x-text-input type="search" wire:model.live.debounce.500ms="search" placeholder="Search by name or position..." />
        </div>
        
        <div>
            <button
                class="btn btn-success"
                wire:click="export"
                wire:target="employees"
            >
                Export to Excel
            </button>
        </div>
    </div>

    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <div>
            <h3>All Employees</h3>
            <p @class('text-secondary mb-0')>
                Overview of pending, in progress, completed requirements of employees
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        <table @class('table')>
            <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary fw-normal') scope="col">Name</th>
                    <th @class('text-secondary fw-normal') scope="col">Position</th>
                    <th @class('text-secondary fw-normal') scope="col">Department</th>
                    <th @class('text-secondary fw-normal') scope="col">Contract Signing</th>
                    <th @class('text-secondary fw-normal') scope="col">HR Documents</th>
                    <th @class('text-secondary fw-normal') scope="col">Employement Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td class="text-nowrap"><strong>{{ $employee['first_name'] . ' ' . $employee['last_name'] }}</strong></td>
                        <td class="text-truncate">{{ $employee['position'] ?? '—' }}</td>
                        <td class="text-capitalize">{{ $employee['department']['name'] ?? 'Not Integrated' }}</td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2 bg-secondary">
                                {{ 'Completed' }}
                            </span>
                        </td>
                        <td class="text-capitalize">{{ 'Not Integrated' }}</td>
                        <td class="text-capitalize">{{ $employee['status'] ?? '---' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" @class('text-center text-muted')>
                            Not integrated
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pb-4">
            {{ $employees->links() }}
        </div>
    </div>

</div>
