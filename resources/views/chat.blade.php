@extends('layouts.app')

@section('content')
<div class="chat-container">
    <div class="user-list">
        <ul>
            @foreach($users as $user)
                <li><a href="#">{{ $user->name }}</a></li>
            @endforeach
        </ul>
    </div>
    <div class="chat-box">
        <div class="message-container">
            <div class="messages">
                @foreach($messages as $message)
                    @if($message->sender_id==auth()->user()->id)
                    <div class="message own-message mb-2">{{ $message->message }}</div>
                    @else
                    <div class="message other-message mb-2">{{ $message->message }}</div>
                    @endif
                @endforeach


                <!-- More messages -->
            </div>
            <form action="{{ route('send-message') }}" method="post">
                <div class="message-input">
                    @csrf
                    @if(auth()->user()->id==1)
                    <input type="hidden" name="receiver_id" value="2">
                    @else
                    <input type="hidden" name="receiver_id" value="1">
                    @endif
                    <input type="text" name="message" class="message-text" placeholder="Type your message">
                    <button class="send-button">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('js')

{{--  <script src="{{ asset('assets/js/chat.js') }}"></script>  --}}
@endsection
