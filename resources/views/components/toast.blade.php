@if(session('status'))
    <div
        aria-live="polite"
        aria-atomic="true"
        @class('position-relative')
    >
        {{-- Toast container in the top right --}}
        <div
            @class('toast-container position-fixed top-0 end-0 p-3')
            style="z-index: 2000;"
            wire:poll.4s="clearStatus"
        >
            @foreach((array) session('status') as $status)
                @php
                    $message = is_array($status) ? ($status['message'] ?? '') : $status;
                    $type = is_array($status) ? ($status['type'] ?? 'success') : 'success';
                    $bgClass = $type === 'error' || $type === 'danger' ? 'text-bg-danger' : 'text-bg-success';
                    $iconClass = $type === 'error' || $type === 'danger' ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill';
                @endphp
                <div
                    @class(['toast show align-items-center border-0', $bgClass])
                    role="alert"
                    aria-live="assertive"
                    aria-atomic="true"
                >
                    <div @class('d-flex')>
                        <div @class('toast-body px-3')>
                            <i @class(['bi me-2', $iconClass])></i>{{ $message }}
                        </div>

                        <button
                            type="button"
                            @class('btn-close btn-close-white me-2 m-auto')
                            data-bs-dismiss="toast"
                            aria-label="Close"
                            wire:click="clearStatus"
                        ></button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif