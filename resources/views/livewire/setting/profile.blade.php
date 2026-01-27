@section('page-title', 'My Profile')
@section('page-subtitle', 'View and manage your personal information')
@section('breadcrumbs', 'Profile')

<div class="container-fluid p-4">
    <div class="row mb-4">
        {{-- Header is handled by layout now --}}
    </div>

    @if(isset($user['details']))
        @php
            $details = $user['details'];
            $dept = $details['department'] ?? [];
        @endphp
        <div class="row g-4">
            <!-- Left Column: Profile Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center h-100 p-4">
                    <div class="card-body">
                         <img src="https://ui-avatars.com/api/?name={{ urlencode($details['first_name'] . ' ' . $details['last_name']) }}&size=150&background=0d6efd&color=fff"
                                class="rounded-circle mb-3 border border-4 border-light shadow-sm" alt="Profile Picture">
                        <h4 class="fw-bold mb-1">{{ $details['first_name'] }} {{ $details['last_name'] }}</h4>
                        <p class="text-muted mb-3">{{ $details['position'] }}</p>
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                            {{ $dept['name'] ?? 'Department' }}
                        </span>
                        
                        <div class="mt-4 text-start">
                             <h6 class="text-uppercase text-muted small fw-bold mb-3">Contact Info</h6>
                             <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> {{ $details['email'] }}</p>
                             <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> {{ $details['phone'] }}</p>
                             <p class="mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i> {{ $details['address'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold">Personal Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Basic Info -->
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">First Name</label>
                                <p class="fw-medium">{{ $details['first_name'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Last Name</label>
                                <p class="fw-medium">{{ $details['last_name'] }}</p>
                            </div>
                             <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Middle Name</label>
                                <p class="fw-medium">{{ $details['middle_name'] ?? '-' }}</p>
                            </div>
                             <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Birth Date</label>
                                <p class="fw-medium">{{ \Carbon\Carbon::parse($details['birth_date'])->format('F d, Y') }}</p>
                            </div>
                             <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Age</label>
                                <p class="fw-medium">{{ $details['age'] }}</p>
                            </div>
                             <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Gender</label>
                                <p class="fw-medium text-capitalize">{{ $details['gender'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Civil Status</label>
                                <p class="fw-medium text-capitalize">{{ $details['civil_status'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Date Hired</label>
                                <p class="fw-medium">{{ \Carbon\Carbon::parse($details['date_hired'])->format('F d, Y') }}</p>
                            </div>

                             <div class="col-12 mt-4">
                                <h6 class="fw-bold border-bottom pb-2 mb-3">Professional Details</h6>
                             </div>
                             
                             <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Employee Status</label>
                                <p class="fw-medium text-capitalize">{{ str_replace('_', ' ', $details['employee_status']) }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Experience</label>
                                <p class="fw-medium">{{ $details['experience'] }}</p>
                            </div>
                            <div class="col-12">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Education</label>
                                <p class="fw-medium">{{ $details['education'] }}</p>
                            </div>
                             <div class="col-12">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Skills</label>
                                <p class="fw-medium" style="white-space: pre-wrap;">{{ $details['skills'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h4 class="alert-heading">Profile Details Unavailable</h4>
            <p>We need you to login again to retrieve your latest profile information.</p>
            <hr>
            <p class="mb-0">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 fw-bold">Click here to logout and login again</button>
                </form>
            </p>
        </div>
    @endif
</div>
