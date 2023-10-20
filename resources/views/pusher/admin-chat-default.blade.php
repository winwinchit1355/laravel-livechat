@extends('layouts.app')
@section('content')
<div class="chat-container">
    <div class="user-list">
        <ul class="list-group list-group-flush">
            @foreach($users as $user)
                <li class="list-group-item"><a  href="{{ route('get-user-messages',Crypt::encrypt($user->id)) }}">{{ $user->name }}</a></li>
            @endforeach
        </ul>
    </div>
    <div class="chat-box">
        <div class="chat">

        </div>
    </div>
</div>

@endsection
@section('js')

{{--  <script src="{{ asset('assets/js/chat.js') }}"></script>  --}}
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

@endsection
