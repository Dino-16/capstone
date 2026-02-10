{{-- View Document Checklist Modal --}}
@if($showViewModal && $viewingChecklist)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>Checklist Details
                </h5>
                <button type="button" class="btn-close" wire:click="$activatePasswordVerification('showViewModal', false)"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <div class="h3 mb-1 fw-bold">{{ $viewingChecklist->employee_name }}</div>
                    <div class="text-secondary">
                        <i class="bi bi-envelope me-1"></i>{{ $viewingChecklist->email }}
                    </div>
                </div>

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Completion Progress</span>
                            <span class="badge bg-{{ $viewingChecklist->getCompletionPercentage() == 100 ? 'success' : 'primary' }}">
                                {{ number_format($viewingChecklist->getCompletionPercentage(), 0) }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $viewingChecklist->getCompletionPercentage() == 100 ? 'success' : 'primary' }}" 
                                 role="progressbar" 
                                 style="width: {{ $viewingChecklist->getCompletionPercentage() }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Required Documents</h6>
                <div class="list-group list-group-flush border rounded overflow-hidden">
                    @php 
                        $docTypes = [
                            'resume' => 'Resume',
                            'medical_certificate' => 'Medical Certificate',
                            'valid_government_id' => 'Valid Government ID',
                            'transcript_of_records' => 'Transcript of Records',
                            'nbi_clearance' => 'NBI Clearance',
                            'barangay_clearance' => 'Barangay Clearance',
                        ];
                    @endphp
                    @foreach($viewingChecklist->documents ?? [] as $type => $status)
                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-{{ $status === 'complete' ? 'success' : 'light' }} bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-file-earmark-{{ $status === 'complete' ? 'check text-success' : 'text text-secondary' }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold mb-0 text-capitalize">{{ $docTypes[$type] ?? str_replace('_', ' ', $type) }}</div>
                                    <small class="text-{{ $status === 'complete' ? 'success' : 'warning' }}">
                                        {{ ucfirst($status) }}
                                    </small>
                                </div>
                            </div>
                            @if($type === 'resume' && $status === 'complete')
                                <button 
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="viewDocument('{{ $type }}', {{ $viewingChecklist->id }})"
                                >
                                    <i class="bi bi-eye"></i> View File
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($viewingChecklist->notes)
                    <div class="mt-4">
                        <h6 class="fw-bold mb-2">Notes</h6>
                        <div class="p-3 bg-light border-start border-primary border-4 rounded">
                            {{ $viewingChecklist->notes }}
                        </div>
                    </div>
                @endif
            </div>

            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-primary px-4" wire:click="$activatePasswordVerification('showViewModal', false)">Done</button>
            </div>
        </div>
    </div>
</div>
@endif
