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
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Given By *</label>
                                <input type="text" class="form-control @error('givenBy') is-invalid @enderror" wire:model="givenBy" placeholder="Your name">
                                @error('givenBy') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
