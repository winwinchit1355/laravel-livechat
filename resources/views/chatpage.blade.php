@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-end">
                    <a href="{{ route('home') }}" class="btn btn-light">Back</a>
                </div>
                <div class="card">
                    <div class="card-header">{{ $chatUser }}</div>

                    <div class="card-body overflow-auto">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @foreach ($messages as $key => $message)
                                <div class=" @if($message->status == 'send')
                                    text-end
                                @elseif ($message->status == 'receive')
                                    text-start
                                @endif">{{ $message->message }}</div>
                                <hr style="border: 1px solid #000">
                        @endforeach
                    </div>
                    <div class="card-header text-end">{{ auth()->user()->name }}</div>
                    <div class="card-footer">
                        <form action="{{ route('chat') }}" method="POST">
                            @csrf
                            <div class="d-flex mb-3">
                                <input type="hidden" name="refer_id" value="{{ $refer_id }}">
                                <input type="text" name="message" id="message" class="form-control" placeholder="Enter a message" aria-describedby="helpId" required>
                                <button type="submit" class="btn"><img src="/send_circle_icon.png" alt="" width="40px"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    var socket = new WebSocket('ws://127.0.0.1:8080');

    socket.onopen = function(e) {
       console.log("Connection established!");
    };

    socket.onmessage = function(e) {
       console.log(e.data);
    };

    function register() {
     conn.send(JSON.stringify({command: "register", userId: "1"}));
    }

    function send() {
     conn.send(JSON.stringify({command: "message", to: "2", from: "1", data: {message: "halo user 2"}}));
    }
   </script>
@endsection
