<div @class('row g-3 mb-3')>
    @foreach ($this->reportTypeCounts as $type => $count)
        <div @class('col-3')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                {{-- Icon --}}
                <div @class('mb-2')>
                    @switch($type)
                        @case('All')
                            <i @class('bi bi-collection-fill text-primary fs-3')></i>
                            @break
                        @case('Requisition')
                            <i @class('bi bi-clipboard-data text-info fs-3')></i>
                            @break
                        @case('Job Post')
                            <i @class('bi bi-briefcase-fill text-success fs-3')></i>
                            @break
                        @case('Employee')
                            <i @class('bi bi-people-fill text-warning fs-3')></i>
                            @break
                        @case('Document Checklist')
                            <i @class('bi bi-file-earmark-check-fill text-secondary fs-3')></i>
                            @break
                        @case('Orientation Schedule')
                            <i @class('bi bi-calendar-check-fill text-danger fs-3')></i>
                            @break
                        @case('Evaluation Records')
                            <i @class('bi bi-star-fill text-warning fs-3')></i>
                            @break
                        @case('Rewards')
                            <i @class('bi bi-trophy-fill text-success fs-3')></i>
                            @break
                        @case('Give Rewards')
                            <i @class('bi bi-gift-fill text-info fs-3')></i>
                            @break
                        @default
                            <i @class('bi bi-file-earmark-fill text-muted fs-3')></i>
                            @break
                    @endswitch
                </div>

                <div @class('ps-2')>
                    {{-- Count --}}
                    <div @class('fw-semi fs-4')>
                        {{ $count }}
                    </div>

                    {{-- Label --}}
                    <div @class('text-muted small')>
                        {{ $type }} reports
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
