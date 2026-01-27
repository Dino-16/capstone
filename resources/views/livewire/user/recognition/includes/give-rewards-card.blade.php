{{-- STATUS CARDS --}}
<div @class('row g-3 mb-3')>
    <div @class('col-md-3')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-gift text-primary fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $rewardsGiven->total() }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Total Given
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-3')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-hourglass-split text-warning fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $rewardsGiven->where('status', 'pending')->count() }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Pending
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
                    {{ $rewardsGiven->where('status', 'approved')->count() }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Approved
                </div>
            </div>
        </div>
    </div>
    <div @class('col-md-3')>
        <div @class('card p-3 shadow-sm border-0 h-100')>
            {{-- Icon --}}
            <div @class('mb-2')>
                <i @class('bi bi-star-fill text-dark fs-3')></i>
            </div>

            <div @class('ps-2')>
                {{-- Count --}}
                <div @class('fw-semi fs-4')>
                    {{ $rewards->count() }}
                </div>

                {{-- Label --}}
                <div @class('text-muted small')>
                    Available Rewards
                </div>
            </div>
        </div>
    </div>
</div>
