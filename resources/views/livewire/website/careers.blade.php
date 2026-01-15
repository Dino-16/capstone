<div @class('py-5')>
    <div @class('d-flex align-items-center justify-content-between flex-column mb-5')>
        <h1 @class('display-3 text-center fw-medium pt-5')>Apply Now and Be Part of Our Team</h1>
        <p @class('display-6 text-center')>Explore thousands of opportunities</p>

        <div @class('input-group mt-4 w-50')>
            <span @class('input-group-text bg-white border-end-0 no-focus') tabindex="0">
                <i @class('bi bi-search text-muted')></i>
            </span>
            <!-- Live search binding -->
            <input type="text" wire:model.debounce.500ms="search"
                @class('form-control border-start-0 no-focus no-hover')
                placeholder="Search jobs here ...">

            <!-- Manual search trigger -->
            <button wire:click="searchJobs" @class('btn btn-warning no-focus') type="button">
                Search
            </button>
        </div>
    </div>
    <div @class('container-fluid px-3 px-md-5 pt-5')>
        <h3 @class('mb-5 px-2 px-md-0')>All Available Jobs</h3>
        <div @class('row')>
            <!-- Job Listings Column -->
            <div @if($showDetails)
                @class('col-md-6')
                @else
                @class('col-md-12')
                @endif>
                <div @class('row g-4')>
                    @forelse($jobs as $job)
                        <div @class('col col-lg-4')>
                            <div @class('card shadow-sm h-100 ' . ($selectedJob && $selectedJob->id === $job->id ? 'border border-3 border-warning' : ''))>
                                <div @class('card-body d-flex flex-column')>
                                    <h4 @class('h5 mb-3')>{{ $job->position }}</h4>
                                    <div @class('mb-3')>
                                        <span @class('badge bg-success me-1 mb-1')>{{ $job->type }}</span>
                                        <span @class('badge bg-primary')>{{ $job->arrangement }}</span>
                                    </div>
                                    <p @class('mb-auto')>{{ $job->description }}</p>

                                    @if($selectedJob && $selectedJob->id === $job->id)
                                        <button @class('btn btn-secondary mt-3') disabled>
                                            View Details <i @class('bi bi-arrow-right')></i>
                                        </button>
                                    @else
                                        <button wire:click="viewDetails({{ $job->id }})" @class('btn btn-primary mt-3')>
                                            View Details <i @class('bi bi-arrow-right')></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div @class('card p-5 d-flex align-items-center justify-content-center')>
                            <div>
                                <i @class('bi bi-search d-block fs-1 text-secondary mb-3 text-center')></i>
                                <h3 @class('text-center text-secondary')>No Available Jobs</h3>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Job Details Column -->
            @if($showDetails && $selectedJob)
                <div @class('col-md-6')>
                    <div @class('card shadow-sm')>
                        <div @class('card-body')>
                            <div @class('d-flex justify-content-between align-items-center')>
                                <h4 @class('h5 mb-3')>{{ $selectedJob->position }}</h4>
                                <span wire:click="remove"><i @class('bi bi-x-circle-fill')></i></span>
                            </div>

                            <div @class('mb-3')>
                                <span @class('badge bg-success me-1 mb-1')>{{ $selectedJob->type }}</span>
                                <span @class('badge bg-primary')>{{ $selectedJob->arrangement }}</span>
                            </div>

                            <div @class('mb-4')>
                                <h5>Job Description</h5>
                                <p>{{ $selectedJob->description }}</p>
                            </div>

                            <div @class('mb-4')>
                                <h5>Job Qualifications</h5>
                                <ul @class('list-unstyled')>
                                    <li @class('mb-2')>{{ $selectedJob->qualifications }}</li>
                                </ul>
                            </div>

                            <div @class('mb-4')>
                                <a @class('btn btn-success') href="{{ route('apply-now', ['id' => $selectedJob->id]) }}">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>