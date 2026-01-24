<div class="bg-white p-5 rounded shadow">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="Travel Jetlounge logo" style="height: 50px;">
    </div>

    @if (session('status'))
        <div class="alert alert-success text-center">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="verifyOtp">
        <div class="mb-3 text-center">
            <h5 class="text-primary fw-semibold">We sent an OTP to your email.</h5>
            <p class="fw-medium">Put it below.</p>
        </div>

        <div class="d-flex justify-content-center gap-2 mb-3">
            @for ($i = 0; $i < 6; $i++)
                <input
                    type="text"
                    maxlength="1"
                    wire:model.lazy="otpDigits.{{ $i }}"
                    class="form-control text-center fs-4 fw-bold border rounded shadow-sm"
                    style="width: 3rem; height: 3rem;"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                />
            @endfor
        </div>

        @error('otpDigits.*')
            <div class="text-danger text-center mb-2">{{ $message }}</div>
        @enderror

        @error('otp')
            <div class="text-danger text-center mb-2">{{ $message }}</div>
        @enderror

        @if (session('user_email'))
            <div class="text-center mb-3">
                <small class="text-muted">
                    Enter the code we sent to <strong>{{ session('user_email') }}</strong>
                </small>
            </div>
        @endif

        <div class="text-center my-3">
            <button type="submit" class="btn btn-primary px-4">
                Verify OTP
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <p class="mb-1">Didn't receive a code?</p>
        <button wire:click="resendOtp" class="btn btn-link p-0">
            Resend OTP
        </button>
    </div>
</div>

{{-- Optional auto-focus script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('input[maxlength="1"]');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
        });
    });
</script>