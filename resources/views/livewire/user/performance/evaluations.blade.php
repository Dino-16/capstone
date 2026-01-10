<div>
    <!-- Header -->
    <div @class('mb-4')>
        <h3 @class('fw-bold')>Performance Evaluations</h3>
        <p @class('text-muted')>Create and manage employee performance evaluations</p>
    </div>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    <!-- Evaluation Form -->
    <div @class('card')>
        <div @class('card-header')>
            <h5 @class('mb-0')>Employee Performance Evaluation Form</h5>
        </div>
        <div @class('card-body p-4')>
            <form wire:submit="addEvaluation">
                            <!-- Employee Information Section -->
                            <div @class('card mb-4')>
                                <div @class('card-header bg-light')>
                                    <h6 @class('mb-0 fw-bold')>Employee Information</h6>
                                </div>
                                <div @class('card-body')>
                                    <div @class('row g-3')>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3 position-relative')>
                                                <label @class('form-label fw-semibold')>Search Employee</label>
                                                <input 
                                                    type="text" 
                                                    @class('form-control') 
                                                    wire:model.live="employeeName" 
                                                    placeholder="Type to search..."
                                                    autocomplete="off"
                                                >
                                                @error('employeeName') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                                
                                                @if($showEmployeeDropdown && count($filteredEmployees) > 0)
                                                    <div @class('position-absolute w-100 bg-white border rounded shadow-lg mt-1') style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                        @foreach($filteredEmployees as $employee)
                                                            <div 
                                                                @class('px-3 py-2 hover:bg-light cursor-pointer')
                                                                wire:click="selectEmployee('{{ $employee['name'] ?? $employee['employee_name'] ?? 'Unknown' }}')"
                                                            >
                                                                <strong>{{ $employee['name'] ?? $employee['employee_name'] ?? 'Unknown' }}</strong>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Email</label>
                                                <input type="email" @class('form-control') wire:model="email" readonly>
                                                @error('email') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Position</label>
                                                <input type="text" @class('form-control') wire:model="position" placeholder="Enter position">
                                                @error('position') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Department</label>
                                                <input type="text" @class('form-control') wire:model="department" placeholder="Enter department">
                                                @error('department') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div @class('row g-3')>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Employment Date</label>
                                                <input type="date" @class('form-control') wire:model="employmentDate">
                                                @error('employmentDate') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Evaluation Date</label>
                                                <input type="date" @class('form-control') wire:model="evaluationDate">
                                                @error('evaluationDate') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Evaluation Type</label>
                                                <select @class('form-select') wire:model="evaluationType">
                                                    <option value="Regular">Regular</option>
                                                    <option value="Probationary">Probationary</option>
                                                    <option value="Annual">Annual</option>
                                                </select>
                                                @error('evaluationType') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div @class('col-md-3')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Evaluator Name</label>
                                                <input type="text" @class('form-control') wire:model="evaluatorName" placeholder="Enter evaluator name">
                                                @error('evaluatorName') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance Rating Section -->
                            <div @class('card mb-4')>
                                <div @class('card-header bg-light')>
                                    <h6 @class('mb-0 fw-bold')>Performance Rating</h6>
                                </div>
                                <div @class('card-body')>
                                    <!-- Rating Key -->
                                    <div @class('bg-secondary bg-opacity-10 p-3 rounded mb-4')>
                                        <h6 @class('fw-bold mb-3')>Rating Key</h6>
                                        <div @class('row')>
                                            <div @class('col-md-2')>
                                                <div @class('mb-2')>
                                                    <span @class('badge bg-success me-2')>100</span>
                                                    <span>Outstanding</span>
                                                </div>
                                            </div>
                                            <div @class('col-md-2')>
                                                <div @class('mb-2')>
                                                    <span @class('badge bg-primary me-2')>90</span>
                                                    <span>Excellent</span>
                                                </div>
                                            </div>
                                            <div @class('col-md-2')>
                                                <div @class('mb-2')>
                                                    <span @class('badge bg-info me-2')>80</span>
                                                    <span>Good</span>
                                                </div>
                                            </div>
                                            <div @class('col-md-2')>
                                                <div @class('mb-2')>
                                                    <span @class('badge bg-warning me-2')>70</span>
                                                    <span>Satisfactory</span>
                                                </div>
                                            </div>
                                            <div @class('col-md-2')>
                                                <div @class('mb-2')>
                                                    <span @class('badge bg-orange me-2') style="background-color: #fd7e14;">60</span>
                                                    <span>Needs Improvement</span>
                                                </div>
                                            </div>
                                            <div @class('col-md-2')>
                                                <div @class('mb-2')>
                                                    <span @class('badge bg-danger me-2')>50</span>
                                                    <span>Poor</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Measurement Criteria Table -->
                                    <div @class('table-responsive mb-4')>
                                        <table @class('table table-bordered')>
                                            <thead @class('table-light')>
                                                <tr>
                                                    <th style="width: 25%">Performance Factor</th>
                                                    <th style="width: 50%">Measurement Criteria</th>
                                                    <th style="width: 25%">Rating</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td @class('fw-semibold')>Job Knowledge</td>
                                                    <td>
                                                        <small @class('text-muted')>
                                                            • Understanding of job requirements<br>
                                                            • Technical skills and abilities<br>
                                                            • Knowledge of policies and procedures<br>
                                                            • Ability to learn new tasks
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <select @class('form-select form-select-sm') wire:model.live="jobKnowledge">
                                                            <option value="100">100 - Outstanding</option>
                                                            <option value="90">90 - Excellent</option>
                                                            <option value="80">80 - Good</option>
                                                            <option value="70">70 - Satisfactory</option>
                                                            <option value="60">60 - Needs Improvement</option>
                                                            <option value="50">50 - Poor</option>
                                                        </select>
                                                        @error('jobKnowledge') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td @class('fw-semibold')>Work Quality</td>
                                                    <td>
                                                        <small @class('text-muted')>
                                                            • Accuracy and completeness of work<br>
                                                            • Attention to detail<br>
                                                            • Follow-up and follow-through<br>
                                                            • Meeting deadlines
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <select @class('form-select form-select-sm') wire:model.live="workQuality">
                                                            <option value="100">100 - Outstanding</option>
                                                            <option value="90">90 - Excellent</option>
                                                            <option value="80">80 - Good</option>
                                                            <option value="70">70 - Satisfactory</option>
                                                            <option value="60">60 - Needs Improvement</option>
                                                            <option value="50">50 - Poor</option>
                                                        </select>
                                                        @error('workQuality') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td @class('fw-semibold')>Initiative</td>
                                                    <td>
                                                        <small @class('text-muted')>
                                                            • Self-motivation and drive<br>
                                                            • Proactive problem-solving<br>
                                                            • Suggesting improvements<br>
                                                            • Taking on additional responsibilities
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <select @class('form-select form-select-sm') wire:model.live="initiative">
                                                            <option value="100">100 - Outstanding</option>
                                                            <option value="90">90 - Excellent</option>
                                                            <option value="80">80 - Good</option>
                                                            <option value="70">70 - Satisfactory</option>
                                                            <option value="60">60 - Needs Improvement</option>
                                                            <option value="50">50 - Poor</option>
                                                        </select>
                                                        @error('initiative') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td @class('fw-semibold')>Communication</td>
                                                    <td>
                                                        <small @class('text-muted')>
                                                            • Clarity in verbal and written communication<br>
                                                            • Listening skills<br>
                                                            • Interpersonal relationships<br>
                                                            • Ability to convey ideas effectively
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <select @class('form-select form-select-sm') wire:model.live="communication">
                                                            <option value="100">100 - Outstanding</option>
                                                            <option value="90">90 - Excellent</option>
                                                            <option value="80">80 - Good</option>
                                                            <option value="70">70 - Satisfactory</option>
                                                            <option value="60">60 - Needs Improvement</option>
                                                            <option value="50">50 - Poor</option>
                                                        </select>
                                                        @error('communication') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td @class('fw-semibold')>Dependability</td>
                                                    <td>
                                                        <small @class('text-muted')>
                                                            • Punctuality and attendance<br>
                                                            • Reliability and consistency<br>
                                                            • Meeting commitments<br>
                                                            • Accountability for actions
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <select @class('form-select form-select-sm') wire:model.live="dependability">
                                                            <option value="100">100 - Outstanding</option>
                                                            <option value="90">90 - Excellent</option>
                                                            <option value="80">80 - Good</option>
                                                            <option value="70">70 - Satisfactory</option>
                                                            <option value="60">60 - Needs Improvement</option>
                                                            <option value="50">50 - Poor</option>
                                                        </select>
                                                        @error('dependability') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td @class('fw-semibold')>Attendance</td>
                                                    <td>
                                                        <small @class('text-muted')>
                                                            • Regular attendance record<br>
                                                            • Timeliness and punctuality<br>
                                                            • Adherence to schedule<br>
                                                            • Proper notification of absences
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <select @class('form-select form-select-sm') wire:model.live="attendance">
                                                            <option value="100">100 - Outstanding</option>
                                                            <option value="90">90 - Excellent</option>
                                                            <option value="80">80 - Good</option>
                                                            <option value="70">70 - Satisfactory</option>
                                                            <option value="60">60 - Needs Improvement</option>
                                                            <option value="50">50 - Poor</option>
                                                        </select>
                                                        @error('attendance') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Overall Score and Status -->
                                    <div @class('row g-3')>
                                        <div @class('col-md-6')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Overall Score</label>
                                                <div @class('input-group')>
                                                    <input type="number" @class('form-control') wire:model="overallScore" min="0" max="100" readonly>
                                                    <span @class('input-group-text bg-primary text-white')>/ 100</span>
                                                </div>
                                                @error('overallScore') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div @class('col-md-6')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Status</label>
                                                <select @class('form-select') wire:model="status">
                                                    <option value="Pending">Pending</option>
                                                    <option value="In Progress">In Progress</option>
                                                    <option value="Completed">Completed</option>
                                                    <option value="Cancelled">Cancelled</option>
                                                </select>
                                                @error('status') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div @class('card mb-4')>
                                <div @class('card-header bg-light')>
                                    <h6 @class('mb-0 fw-bold')>Comments</h6>
                                </div>
                                <div @class('card-body')>
                                    <div @class('row g-3')>
                                        <div @class('col-md-6')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Strengths</label>
                                                <textarea @class('form-control') wire:model="strengths" rows="4" placeholder="Employee's strengths..."></textarea>
                                                @error('strengths') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div @class('col-md-6')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Areas for Improvement</label>
                                                <textarea @class('form-control') wire:model="areasForImprovement" rows="4" placeholder="Areas needing improvement..."></textarea>
                                                @error('areasForImprovement') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div @class('row g-3')>
                                        <div @class('col-md-6')>
                                            <div @class('mb-3')>
                                                <label @class('form-label fw-semibold')>Evaluator Comments</label>
                                                <textarea @class('form-control') wire:model="comments" rows="4" placeholder="Additional comments..."></textarea>
                                                @error('comments') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                <div @class('d-flex justify-content-end m-4')>
                    <button type="submit" @class('btn btn-primary')>
                        <i class="bi bi-check-circle me-2"></i>Save Evaluation
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
