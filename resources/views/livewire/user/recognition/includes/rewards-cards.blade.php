<div @class('row g-3 mb-3')>
    @foreach ($statusCounts as $status => $count)
        <div @class('col-3')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                {{-- Icon --}}
                <div @class('mb-2')>
                    @switch($status)
                        @case('Active')
                            <i @class('bi bi-check-circle-fill text-success fs-3')></i>
                            @break
                        @case('Draft')
                            <i @class('bi bi-journal-text text-warning fs-3')></i>
                            @break
                        @case('Inactive')
                            <i @class('bi bi-x-circle-fill text-danger fs-3')></i>
                            @break
                        @case('All')
                            <i @class('bi bi-collection-fill text-dark fs-3')></i>
                            @break
                    @endswitch
                </div>

                <div @class('ps-2')>
                    {{-- Count --}}
                    <div @class='fw-semi fs-4'>
                        {{ $count }}
                    </div>

                    {{-- Label --}}
                    <div @class='text-muted small'>
                        {{ $status }} rewards
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
