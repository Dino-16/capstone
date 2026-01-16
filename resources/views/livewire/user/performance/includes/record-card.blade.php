<div @class('row g-3 mb-3')>
    <div @class('col-md-3')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-collection-fill fs-3')></i>
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
    <div @class('col-md-3')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-clock text-primary fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $stats['ongoing'] }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Ongoing
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-3')>
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
    <div @class('col-md-3')>
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
                    Evaluation Draft
                </div>
            </div>
        </div>
    </div>
</div>