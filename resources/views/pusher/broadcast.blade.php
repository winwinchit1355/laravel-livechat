<div class="right message" data-messageId="{{ \Crypt::encrypt($message_id) }}">
    {{--  <img class="avatar-img" src="https://cdn-icons-png.flaticon.com/512/6596/6596121.png" width="100px" alt="">  --}}
    @if($sender_id == auth()->user()->id)
        @if(isset($filePath) && $filePath != '')
        <img class="message-image" src="{{ asset($filePath) }}" alt="" >
        <br>
        @endif
        @if(isset($message) && $message != '')
        <p>{{ $message }}</p>
        @endif
    @endif
</div>
