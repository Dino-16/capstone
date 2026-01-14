<div @class(['p-5', 'bg-light'])>
    {{-- SUCCESS TOAST --}}
    @if($showSuccessToast)
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            <div class="card border-0 shadow-lg bg-success text-white rounded-4 animate__animated animate__fadeInRight" 
                 style="min-width: 320px;"
                 wire:poll.5s="$set('showSuccessToast', false)">
                <div class="card-body d-flex align-items-center p-3">
                    <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Success!</h6>
                        <p class="mb-0 small">Application submitted successfully.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" wire:click="$set('showSuccessToast', false)"></button>
                </div>
            </div>
        </div>
    @endif

    <a @class(['nav-link', 'text-secondary', 'mb-4']) href="{{ route('careers') }}">
        <i class="bi bi-arrow-left-circle me-2"></i>Back to Jobs
    </a>

    <div @class(['container', 'bg-white', 'p-5', 'rounded-4', 'shadow-sm'])>
        <div class="border-bottom mb-5 pb-3">
            <h2 class="fw-bold mb-1">Application Form</h2>
            <p class="text-muted">Position: <span style="color: #213A5C;" class="fw-semibold">{{ $job->position }}</span></p>
        </div>

        {{-- Show Database Errors if any --}}
        @error('submission')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <form wire:submit.prevent="submitApplication">
            <div @class(['row', 'g-4'])>
                
                {{-- Personal Information --}}
                <div class="col-12">
                    <h5 class="text-uppercase tracking-wider text-secondary fw-bold small mb-3">Personal Information</h5>
                </div>

                <div class="col-md-4">
                    <x-input-label for="first-name" :value="__('First Name')" />
                    <x-text-input wire:model="applicantFirstName" type="text" id="first-name" class="form-control-lg" />
                    <x-input-error field="applicantFirstName" />
                </div>

                <div class="col-md-3">
                    <x-input-label for="middle-name" :value="__('Middle Name')" />
                    <x-text-input wire:model="applicantMiddleName" type="text" id="middle-name" class="form-control-lg" />
                    <x-input-error field="applicantMiddleName" />
                </div>

                <div class="col-md-3">
                    <x-input-label for="last-name" :value="__('Last Name')" />
                    <x-text-input wire:model="applicantLastName" type="text" id="last-name" class="form-control-lg" />
                    <x-input-error field="applicantLastName" />
                </div>

                <div class="col-md-2">
                    <x-input-label for="suffix-name" :value="__('Suffix')" />
                    <x-text-input wire:model="applicantSuffixName" type="text" id="suffix-name" class="form-control-lg" placeholder="Jr." />
                </div>

                <div class="col-md-6">
                    <x-input-label for="applicant-email" :value="__('Email Address')" />
                    <x-text-input wire:model="applicantEmail" type="email" id="applicant-email" class="form-control-lg" />
                    <x-input-error field="applicantEmail" />
                </div>

                <div class="col-md-6">
                    <x-input-label for="applicant-phone" :value="__('Phone Number')" />
                    <x-text-input wire:model="applicantPhone" type="text" id="applicant-phone" class="form-control-lg" />
                    <x-input-error field="applicantPhone" />
                </div>

                {{-- Address Section --}}
                <div class="col-12 mt-5">
                    <h5 class="text-uppercase tracking-wider text-secondary fw-bold small mb-3">Current Address</h5>
                </div>

                <div class="col-md-6">
                    <x-input-label for="region" :value="__('Region')" />
                    <select wire:model.live="selectedRegion" id="region" class="form-select form-select-lg">
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error field="selectedRegion" />
                </div>

                <div class="col-md-6">
                    <x-input-label for="province" :value="__('Province')" />
                    <select wire:model.live="selectedProvince" id="province" class="form-select form-select-lg" 
                        {{ count($provinces) > 0 || $selectedRegion === '130000000' ? '' : 'disabled' }}>
                        @if($selectedRegion === '130000000')
                            <option value="NCR">Metropolitan Manila (NCR)</option>
                        @else
                            <option value="">Select Province</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                    <x-input-error field="selectedProvince" />
                </div>

                <div class="col-md-6">
                    <x-input-label for="city" :value="__('City / Municipality')" />
                    <select wire:model.live="selectedCity" id="city" class="form-select form-select-lg" {{ empty($cities) ? 'disabled' : '' }}>
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city['code'] }}">{{ $city['name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error field="selectedCity" />
                </div>

                <div class="col-md-6">
                    <x-input-label for="barangay" :value="__('Barangay')" />
                    <select wire:model.live="selectedBarangay" id="barangay" class="form-select form-select-lg" {{ empty($barangays) ? 'disabled' : '' }}>
                        <option value="">Select Barangay</option>
                        @foreach($barangays as $barangay)
                            <option value="{{ $barangay['name'] }}">{{ $barangay['name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error field="selectedBarangay" />
                </div>

                <div class="col-12">
                    <x-input-label for="house-street" :value="__('House No. / Street')" />
                    <x-text-input wire:model="houseStreet" type="text" id="house-street" class="form-control-lg" />
                    <x-input-error field="houseStreet" />
                </div>

                {{-- File Upload --}}
                <div class="col-12 mt-5">
                    <h5 class="text-uppercase tracking-wider text-secondary fw-bold small mb-3">Professional Documents</h5>
                    @if (!$applicantResumeFile)
                        <label for="resume" class="w-100">
                            <input wire:model="applicantResumeFile" type="file" id="resume" class="d-none" />
                            <div class="upload-box d-flex flex-column justify-content-center align-items-center p-4 border border-2 border-dashed rounded-4 bg-light text-center" style="cursor: pointer; min-height: 150px;">
                                <i class="bi bi-file-earmark-pdf fs-1 text-primary mb-2"></i>
                                <span class="fw-bold">Click to upload Resume</span>
                            </div>
                        </label>
                    @else
                        <div class="alert alert-info d-flex justify-content-between align-items-center rounded-4">
                            <span><i class="bi bi-file-earmark-check me-2"></i>{{ $applicantResumeFile->getClientOriginalName() }}</span>
                            <button type="button" wire:click="removeResume" class="btn btn-sm btn-danger rounded-pill">Remove</button>
                        </div>
                    @endif
                    <x-input-error field="applicantResumeFile" />
                </div>

                {{-- Submission --}}
                <div class="col-12 mt-5 border-top pt-4">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agreed-to-terms" wire:model="agreedToTerms">
                        <label class="form-check-label ms-2" for="agreed-to-terms">
                            I agree to the <a href="javascript:void(0)" wire:click="$toggle('showTerms')" class="fw-bold text-primary">terms and conditions</a>.
                        </label>
                    </div>
                    @error('agreedToTerms') <p class="text-danger small">{{ $message }}</p> @enderror

                    @if($showTerms)
                        <div class="bg-light p-4 rounded-3 border mb-4 shadow-sm animate__animated animate__fadeInUp">
                            <h6 class="fw-bold">Privacy Statement</h6>
                            <p class="small text-muted">Your data is processed for recruitment only...</p>
                        </div>
                    @endif

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" style="background-color: #213A5C; border: none;">
                            <span wire:loading.remove wire:target="submitApplication">Submit Application</span>
                            <span wire:loading wire:target="submitApplication">Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>