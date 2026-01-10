<div @class('row g-3 mb-3')>
    <div @class('col-md-2')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-collection-fill text-primary fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $stats['total'] }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Total Evaluations
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-2')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-hourglass-split text-warning fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $stats['pending'] }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Pending
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-2')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-check-circle-fill text-success fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $stats['completed'] }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Completed
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-2')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-star-fill text-info fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ round($stats['average_score'], 1) }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Avg Score
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-2')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-calendar text-secondary fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $stats['this_month'] }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    This Month
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-2')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-journal-text text-secondary fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $stats['draft'] }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Draft
                </div>
            </div>
        </div>
    </div>
</div>