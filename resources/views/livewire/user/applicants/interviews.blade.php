<div>
@section('page-title', 'Interview Assessment')
@section('page-subtitle', 'Conduct interviews and practical exams')
@section('breadcrumbs', 'Interviews')

<div @class('pt-2')>
    {{-- TOAST --}}
    <x-toast />


    {{-- INTERVIEW ASSESSMENT FORM --}}
    <div @class('card')>
        <div @class('card-header')>
            <h5 @class('mb-0')>Interview Assessment</h5>
        </div>
        <div @class('card-body p-4')>
            <form wire:submit="submitInterview">
                    <!-- Candidate Information Section -->
                    <div @class('card mb-4')>
                        <div @class('card-header bg-light')>
                            <h6 @class('mb-0 fw-bold')>Candidate Information</h6>
                        </div>
                        <div @class('card-body')>
                            <div @class('row g-3')>
                                <div @class('col-md-6')>
                                    <div @class('mb-3 position-relative')>
                                        <label @class('form-label fw-semibold')>Search Candidate</label>
                                        <input 
                                            type="text" 
                                            @class('form-control') 
                                            wire:model.live="candidateName" 
                                            placeholder="Type to search..."
                                            autocomplete="off"
                                        >
                                        @error('candidateName') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                        
                                        @if($showCandidateDropdown && count($filteredCandidates) > 0)
                                            <div @class('position-absolute w-100 bg-white border rounded shadow-lg mt-1') style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                @foreach($filteredCandidates as $candidate)
                                                    <div 
                                                        @class('px-3 py-2 hover:bg-light cursor-pointer')
                                                        wire:click="selectCandidateFromSearch('{{ $candidate['candidate_name'] }}')"
                                                    >
                                                        <strong>{{ $candidate['candidate_name'] }}</strong>
                                                        <br>
                                                        <small @class('text-muted')>{{ $candidate['candidate_email'] }} - {{ $candidate['applied_position'] ?? 'N/A' }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($showCandidateDropdown && count($filteredCandidates) == 0)
                                            <div @class('position-absolute w-100 bg-white border rounded shadow-lg mt-1') style="z-index: 1000;">
                                                <div @class('px-3 py-4 text-center text-muted')>
                                                    <i @class('bi bi-person-x fs-3 d-block mx-auto mb-2')></i>
                                                    Candidate not found
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div @class('col-md-6')>
                                    <div @class('mb-3')>
                                        <label @class('form-label fw-semibold')>Position Applied For</label>
                                        <select @class('form-select') 
                                                wire:model="selectedPosition">
                                            <option value="">Select a position...</option>
                                            @foreach($jobs as $job)
                                                <option value="{{ $job->position }}">{{ $job->position }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedPosition') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interview Questions Section -->
                    <div @class('card mb-4')>
                        <div @class('card-header bg-light')>
                            <h6 @class('mb-0 fw-bold')>Interview Questions</h6>
                        </div>
                        <div @class('card-body')>
                            @if(!$candidateName)
                                <div @class('alert alert-warning')>
                                    No interview questions available. Please enter candidate name first.
                                </div>
                            @elseif(count($interviewQuestions) > 0)
                                @foreach($interviewQuestions as $index => $question)
                                    <div @class('mb-4')>
                                        <label @class('form-label fw-semibold')>Question {{ $index + 1 }}</label>
                                        <p @class('text-muted mb-2')>{{ $question }}</p>
                                        <textarea @class('form-control') 
                                                  wire:model="interviewAnswers.{{ $index }}"
                                                  rows="3"
                                                  placeholder="Enter candidate's answer..."></textarea>
                                        @error('interviewAnswers.' . $index) <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                    </div>
                                @endforeach
                            @else
                                <div @class('alert alert-warning')>
                                    No interview questions available for this position.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Practical Examination Section -->
                    <div @class('card mb-4')>
                        <div @class('card-header bg-light')>
                            <h6 @class('mb-0 fw-bold')>Practical Examination</h6>
                        </div>
                        <div @class('card-body')>
                            @if(!$candidateName)
                                <div @class('alert alert-warning')>
                                    No practical exams available. Please enter candidate name first.
                                </div>
                            @elseif(count($practicalExams) > 0)
                                @foreach($practicalExams as $index => $exam)
                                    <div @class('mb-4')>
                                        <label @class('form-label fw-semibold')>Practical Task {{ $index + 1 }}</label>
                                        <p @class('text-muted mb-2')>{{ $exam }}</p>
                                        <textarea @class('form-control') 
                                                  wire:model="practicalAnswers.{{ $index }}"
                                                  rows="4"
                                                  placeholder="Enter candidate's practical exam response or solution..."></textarea>
                                        @error('practicalAnswers.' . $index) <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                    </div>
                                @endforeach
                            @else
                                <div @class('alert alert-warning')>
                                    No practical exams available for this position.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div @class('d-flex justify-content-end m-4')>
                        <button type="submit" @class('btn btn-primary')>
                            <i class="bi bi-check-circle me-2"></i>Submit Interview Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
