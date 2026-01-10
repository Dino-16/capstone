@if(session('status'))
    <div
        aria-live="polite"
        aria-atomic="true"
        @class('position-relative')
    >
        {{-- Toast container in the top right --}}
        <div
            @class('toast-container text-bg-success rounded top-0 end-0 p-3')
            style="z-index: 2000;"
            wire:poll.3s="clearStatus"
        >
            @foreach((array) session('status') as $message)
                <div
                    @class('')
                    role="alert"
                    aria-live="assertive"
                    aria-atomic="true"
                    data-bs-delay="3000"
                    data-bs-autohide="true"
                >
                    <div @class('d-flex')>
                        <div @class('toast-body px-3')>
                            <span><i @class('bi bi-check-circle-fill me-2')></i></span>{{ $message }}
                        </div>

                        <button
                            type="button"
                            @class('btn-close btn-close-white me-2 m-auto')
                            data-bs-dismiss="toast"
                            aria-label="Close"
                        ></button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif