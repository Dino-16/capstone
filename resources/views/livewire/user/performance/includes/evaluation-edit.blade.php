@if($showEditModal)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(15, 35, 85, 0.5); backdrop-filter: blur(4px);">
    <div @class('modal-dialog modal-lg modal-dialog-centered')>
        <div @class('modal-content border-0 shadow-lg rounded-3')>
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <h4 class="fw-bold text-dark m-0" style="letter-spacing: -0.01em;">Edit Evaluation</h4>
                <button type="button" class="btn-close small shadow-none" wire:click="$set('showEditModal', false)"></button>
            </div>

            <form wire:submit.prevent="updateEvaluation">
                <div @class('modal-body px-4 py-4')>
                    <div @class("row g-4 mb-5")>
                        <div @class("col-md-6")>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Employee Name</span>
                            <input 
                                type="text" 
                                @class("form-control border-light-subtle shadow-none") 
                                wire:model="employeeName" 
                                placeholder="Employee Name"
                                required
                            >
                            @error('employeeName') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        </div>
                        <div @class("col-md-6")>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Email</span>
                            <input 
                                type="email" 
                                @class("form-control border-light-subtle shadow-none") 
                                wire:model="email" 
                                placeholder="employee@example.com"
                            >
                            @error('email') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div @class("row g-4 mb-5")>
                        <div @class("col-md-4")>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Evaluation Date</span>
                            <input 
                                type="datetime-local" 
                                @class("form-control border-light-subtle shadow-none") 
                                wire:model="evaluationDate"
                                required
                            >
                            @error('evaluationDate') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        </div>
                        <div @class("col-md-4")>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Employment Date</span>
                            <input 
                                type="date" 
                                @class("form-control border-light-subtle shadow-none") 
                                wire:model="employmentDate"
                            >
                            @error('employmentDate') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        </div>
                        <div @class("col-md-4")>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Evaluator Name</span>
                            <input 
                                type="text" 
                                @class("form-control border-light-subtle shadow-none") 
                                wire:model="evaluatorName" 
                                placeholder="Evaluator Name"
                                required
                            >
                            @error('evaluatorName') <div @class("invalid-feedback")>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div @class("row g-4 mb-5")>
                        <div @class('col-md-6')>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Overall Score</span>
                            <input 
                                type='number' 
                                @class('form-control border-light-subtle shadow-none') 
                                wire:model="overallScore"
                                min='0' 
                                max='100'
                                step='0.1'
                                placeholder='0-100'
                                required
                            >
                            @error('overallScore') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        </div>
                        <div @class('col-md-6')>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Status</span>
                            <select @class('form-select border-light-subtle shadow-none') wire:model="status">
                                <option value='Draft'>Draft</option>
                                <option value='Pending'>Pending</option>
                                <option value='Completed'>Completed</option>
                            </select>
                            @error('status') <div @class("invalid-feedback")>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div @class('pt-4 border-top')>
                        @error('performanceAreas')
                            <div @class('alert alert-danger py-2 mb-3')>{{ $message }}</div>
                        @enderror
                        @error('notes')
                            <div @class('alert alert-danger py-2 mb-3')>{{ $message }}</div>
                        @enderror
                    </div>

                    <div @class('row g-4 mb-5')>
                        <div @class('col-md-6')>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Performance Areas</span>
                            <textarea 
                                @class('form-control border-light-subtle shadow-none') 
                                wire:model="performanceAreas" 
                                rows="4" 
                                placeholder="Describe performance areas..."
                                required
                            ></textarea>
                            @error('performanceAreas') <div @class("invalid-feedback")>{{ $message }}</div> @enderror
                        </div>
                        <div @class('col-md-6')>
                            <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Notes</span>
                            <textarea 
                                @class('form-control border-light-subtle shadow-none') 
                                wire:model="notes" 
                                rows="3" 
                                placeholder="Additional notes..."
                            ></textarea>
                            @error('notes') <div @class("invalid-feedback")>{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div @class("modal-footer")>
                    <button type='button' @class("btn btn-secondary") wire:click="$set('showEditModal', false)">Cancel</button>
                    <button type='submit' @class("btn btn-primary")>
                        <span wire:loading.remove wire:target='updateEvaluation'>Update Evaluation</span>
                        <span wire:loading wire:target='updateEvaluation' @class("spinner-border spinner-border-sm")></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
