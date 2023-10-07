<div class="left message">
    {{--  <img class="avatar-img" src="https://cdn-icons-png.flaticon.com/512/6596/6596121.png" width="100px"  alt="">  --}}
    @if(isset($filePath) && $filePath != '')
        <img class="message-image" src="{{ asset($filePath) }}" alt="" >
        <br>
    @endif
    @if(isset($message) && $message != '' )
    <p>{{ $message }}</p>
    @endif
</div>
