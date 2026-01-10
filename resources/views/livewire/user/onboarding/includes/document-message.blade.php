@if($showMessageModal)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div @class('modal-dialog modal-dialog-centered')>
        <div @class('modal-content border-0 shadow-lg')>
            <div @class('modal-header')>
                <h5 @class('modal-title fw-bold')>Send Message</h5>
                <button type="button" @class('btn-close') wire:click="$set('showMessageModal', false)"></button>
            </div>

            <form wire:submit.prevent="sendMessage">
                <div @class('modal-body p-4')>
                    <div @class('mb-3')>
                        <label @class('form-label fw-bold')>To</label>
                        <div @class('bg-light p-3 rounded')>
                            <strong>{{ $messageEmployee->employee_name }}</strong>
                            @if($messageEmployee->email)
                                <br><small @class('text-muted')>{{ $messageEmployee->email }}</small>
                            @endif
                        </div>
                    </div>

                    <div @class('mb-3')>
                        <label @class('form-label fw-bold')>Subject</label>
                        <input 
                            type="text" 
                            @class('form-control') 
                            wire:model="messageSubject" 
                            placeholder="Enter message subject"
                        >
                        @error('messageSubject') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                    </div>

                    <div @class('mb-3')>
                        <label @class('form-label fw-bold')>Message</label>
                        <textarea 
                            @class('form-control') 
                            wire:model="messageContent" 
                            rows="6" 
                            placeholder="Type your message here..."
                        ></textarea>
                        @error('messageContent') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        <small @class('text-muted')>Minimum 10 characters</small>
                    </div>
                </div>

                <div @class('modal-footer')>
                    <button type="button" @class('btn btn-secondary') wire:click="$set('showMessageModal', false)">Cancel</button>
                    <button type="submit" @class('btn btn-primary')>
                        <span wire:loading.remove wire:target="sendMessage">Send Message</span>
                        <span wire:loading wire:target="sendMessage" @class('spinner-border spinner-border-sm')></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif