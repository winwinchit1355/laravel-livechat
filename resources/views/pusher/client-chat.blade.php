@extends('layouts.app')
@section('css')

<style>
    body{
        margin-top:20px;
        background:#eee;
    }
    {{--  .box {
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
    }  --}}
    .message-box{
        display: flex;
        align-items: center;
    }
    .message-input, .message-input input{
        width: 100% !important;
    }


</style>
@endsection
@section('content')
<div class="chat">
    <div class="top">
        <img src="https://cdn-icons-png.flaticon.com/512/6596/6596121.png" width="100px"  alt="">
        <div>
            <p>WinWinChit</p>
            <small>Online</small>
        </div>
    </div>
    <div class="messages">
        @include('pusher.receive',['message'=>'Welcome chat!'])
    </div>
    <div class="bottom">
        <form action="" id="message_form">
            <div class="message-box">
                <div class="file-upload ">
                    <span class="file-upload btn border-0">
                        <i class="fa fa-plus"></i>
                    </span>
                </div>
                <div class="message-input">
                    <input type="text" id="message" name="message" autocomplete="off">
                </div>
                <div class="send">
                    <button type="submit" class="border-0"></button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection
@section('js')

{{--  <script src="{{ asset('assets/js/chat.js') }}"></script>  --}}
<script>
    var pusher = new Pusher('0a18b98ebbd0c7def518', {
        cluster: 'ap1'
      });
    var channel = pusher.subscribe('public');
    channel.bind('chat', function(data) {
        var url="{{ route('receive') }}";
        $.post(url,{
            _token:'{{ csrf_token() }}',
            message:data.message,
        }).done(function(res){
            $('.messages > .message').last().after(res);
            $(document).scrollTop($(document).height());
        })
      });
    $('#message_form').submit(function(event){
        event.preventDefault();
        var url="{{ route('broadcast') }}";
        $.ajax({
            url:url,
            method:'POST',
            headers:{
                'X-Socket-Id':pusher.connection.socket_id
            },
            data:{
                _token:'{{ csrf_token() }}',
                message:$("form #message").val()
            }
        }).done(function(res){
            $('.messages > .message').last().after(res);
            $('form #message').val('');
            $(document).scrollTop($(document).height());
        })
    })
</script>

@endsection
