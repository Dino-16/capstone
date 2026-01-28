<div class="container py-5">
    <div class="row min-vh-50 justify-content-center align-items-center">
        <div class="col-12 text-center mb-5">
            <h2 class="fw-bold display-5 text-primary">HR Portals</h2>
            <p class="text-muted">Select your department to login</p>
        </div>

        <div class="col-12">
            <div class="row g-4 justify-content-center">
                <!-- HR 1 Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-people-fill text-primary display-4"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">HR 1</h4>
                            <p class="card-text text-muted small mb-4">Human Resources Department System 1</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 rounded-pill">
                                Login to HR 1
                            </a>
                        </div>
                    </div>
                </div>

                <!-- HR 2 Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-building-gear text-success display-4"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">HR 2</h4>
                            <p class="card-text text-muted small mb-4">Human Resources Department System 2</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-success w-100 rounded-pill">
                                Login to HR 2
                            </a>
                        </div>
                    </div>
                </div>

                <!-- HR 3 Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-person-workspace text-warning display-4"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">HR 3</h4>
                            <p class="card-text text-muted small mb-4">Human Resources Department System 3</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-warning w-100 rounded-pill">
                                Login to HR 3
                            </a>
                        </div>
                    </div>
                </div>

                <!-- HR 4 Card -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-graph-up-arrow text-danger display-4"></i>
                            </div>
                            <h4 class="card-title fw-bold mb-3">HR 4</h4>
                            <p class="card-text text-muted small mb-4">Human Resources Department System 4</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-danger w-100 rounded-pill">
                                Login to HR 4
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-shadow {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
        }
    </style>
</div>
