@extends('layouts.app')
@section('content')
<div class="chat-container">
    <div class="user-list">
        <ul class="list-group list-group-flush">
            @foreach($users as $user)
                <li class="list-group-item {{ $receiver_id==$user->id?'active':'' }}"><a  href="{{ route('get-user-messages',Crypt::encrypt($user->id)) }}">{{ $user->name }}</a><span class="badge bg-danger">10</span></li>
            @endforeach
        </ul>
    </div>
    <div class="chat-box">
        <div class="chat">
            <div class="messages">
                @foreach($oldMessages as $oldMessage)
                    @if($oldMessage->sender_id == Auth::id())
                        <div class="right message">
                            {{--  <img class="avatar-img" src="https://cdn-icons-png.flaticon.com/512/6596/6596121.png" width="100px" alt="">  --}}
                            @if(isset($oldMessage->file) && $oldMessage->file != null)
                            <img class="message-image" src="{{ asset($oldMessage->file) }}" alt="">
                            <br>
                            @endif
                            @if($oldMessage->message != null)
                            <p>{{ $oldMessage->message }}</p>
                            @endif
                        </div>
                    @else
                    <div class="left message">
                        {{--  <img class="avatar-img" src="https://cdn-icons-png.flaticon.com/512/6596/6596121.png" width="100px"  alt="">  --}}
                        @if(isset($oldMessage->file) && $oldMessage->file != null)
                        <img class="message-image" src="{{ $oldMessage->file }}" alt="" width="100%">
                            <br>
                        @endif
                        @if($oldMessage->message != null)
                        <p>{{ $oldMessage->message }}</p>
                        @endif
                    </div>
                    @endif
                @endforeach
                @include('pusher.receive',['message'=>''])
            </div>
            <div class="bottom">
                <form action="" id="message_form" enctype="multipart/form-data" >
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ \Crypt::encrypt($receiver_id) }}" >
                    <div class="message-box">
                        <div class="file-upload ">
                            <span class="btn border-0" id="file-upload">
                                <i class="fa fa-plus"></i>
                            </span>
                            <input type="file" name="file" id="fileInput" style="display: none" />
                        </div>
                        <div class="message-input">
                            <div>
                                <img id="previewImage" height="80" src="" alt="Preview" style="display: none;">
                            </div>
                            <div>
                                <input type="text" id="message" name="message" autocomplete="off">
                            </div>
                        </div>
                        <div class="send">
                            <button type="submit" class="border-0"></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')

{{--  <script src="{{ asset('assets/js/chat.js') }}"></script>  --}}
<script>
    ()=>{
        $(document).scrollTop($(document).height());
    }
    var pusher = new Pusher('0a18b98ebbd0c7def518', {
        cluster: 'ap1'
      });
    var reciever_id={!! json_encode(Auth::id()) !!};
    var channelName='public'+reciever_id;
    var channel = pusher.subscribe(channelName);
    channel.bind('chat', function(data) {
        var url="{{ route('receive') }}";
        $.post(url,{
            _token:'{{ csrf_token() }}',
            message:data.message,
            sender_id:data.sender_id,
            filePath:data.filePath,
        }).done(function(res){
            var selected_id={!! $receiver_id !!};
            if(selected_id == data.sender_id)
            {
                $('.messages > .message').last().after(res);
                $(document).scrollTop($(document).height());
            }
        })
      });
    $('#message_form').submit(function(event){
        event.preventDefault();
        var url="{{ route('broadcast') }}";
        var form = $(this)[0];
        var formData = new FormData(form);
        {{--  formData.forEach(function (value, key) {
            console.log(key, value);
        });  --}}
        $.ajax({
            url:url,
            method:'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers:{
                'X-Socket-Id':pusher.connection.socket_id
            },

        }).done(function(res){
            const previewImage = document.getElementById('previewImage');
            previewImage.style.display='none';
            $('.messages > .message').last().after(res);
            $('form #message').val('');
            $(document).scrollTop($(document).height());
        })
    });
    {{--  file upload   --}}
    document.getElementById('file-upload').addEventListener('click', () => {
        document.getElementById('fileInput').click();

      });
      $('#fileInput').on('change',function(){
        const previewImage = document.getElementById('previewImage');
        const fileInput = document.getElementById('fileInput');
        if (fileInput.files.length > 0) {
            const selectedFile = fileInput.files[0];

            // Check if the selected file is an image
            if (selectedFile.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    // Set the source of the image element to the selected file's data URL
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block'; // Show the image
                };

                // Read the selected file as a data URL
                reader.readAsDataURL(selectedFile);
            } else {
                // Handle cases where the selected file is not an image
                alert('Please select an image file.');
            }
        }
      })



</script>

@endsection
