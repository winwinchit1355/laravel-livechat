@extends('layouts.app')
@section('css')
<style>
    body{
        margin-top:20px;
        background:#eee;
    }
    .box {
        position: relative;
        border-radius: 3px;
        background: #ffffff;
        border-top: 3px solid #d2d6de;
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .box.box-primary {
        border-top-color: #3c8dbc;
    }

    .box-body {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        padding: 10px;
    }

    .direct-chat .box-body {
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
        position: relative;
        overflow-x: hidden;
        padding: 0;
    }
    .direct-chat-messages {
        padding: 10px;
        height: 150px;
        overflow: auto;
    }
</style>
@endsection
@section('content')
{{--  <div class="chat-container">
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
            <form id="chat-form">
                <div class="message-input">
                    @if(auth()->user()->id==1)
                    <input type="hidden" name="receiver_id" value="2">
                    @else
                    <input type="hidden" name="receiver_id" value="1">
                    @endif
                    <input type="text" id="message" name="message" class="message-text" placeholder="Type your message">
                    <button type="submit" id="send_message" class="send-button">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>  --}}
<div class="app">
    <div class="row">

        <div class="col-sm-6 offset-sm-3 my-2">
            <input type="text" class="form-control" name="username" id="username" placeholder="Enter a user ..........">
        </div>

        <div class="col-sm-6 offset-sm-3">
            <div class="box box-primary direct-chat direct-chat-primary">

                <div class="box-body">
                    <div class="direct-chat-messages" id="messages"></div>
                </div>

                <div class="box-footer">
                    <form action="#" method="post" id="message_form">
                        <div class="input-group">
                            <input type="text" name="message" id="message" placeholder="Type Message ..." class="form-control">
                            <span class="input-group-btn">
                                <button type="submit" id="send_message"  class="btn btn-primary btn-flat">Send</button>
                          </span>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
@section('js')

{{--  <script src="{{ asset('assets/js/chat.js') }}"></script>  --}}
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

@endsection
