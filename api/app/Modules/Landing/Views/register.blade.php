@extends('Landing::share.base')
@section('content')

<div class="row-fluid">
	@include('Landing::share.side')
	<div class="span9">
		<div class="b-bar-home uppercase">
	        <h1>Đăng ký/liên hệ</h1>
	    </div>

		<div class="border-content-home padding-ct">
            <div style="margin: 5px; color: #000;">
                <div style="text-align: justify; line-height: 19px; margin-bottom: 8px;">
                    <div>
                        <p>
                            <strong>Văn phòng:</strong> {{ConfigDb::get('contact-address')}}
                        </p>
                        <p>
                            <strong>Phone:</strong> {{ConfigDb::get('contact-phone')}}
                        <p>
                        <p>
                            <strong>Email:</strong> <a href="mailto:{{ConfigDb::get('contact-email')}}">{{ConfigDb::get('contact-email')}}</a>
                        </p>
                    </div>
                </div>
                <div style="height: 15px;"></div>
                @if($status !== 'new')
                    @if($status === 'success')
                        <div class="alert alert-success">{{ $message }}</div>
                    @else
                        <div class="alert alert-error">{{ $message }}</div>
                    @endif
                @endif
                <div>
                    <iframe
                        src="{{config('app.base_url')}}user/signup"
                        style="width: 100%; height: 500px"
                        frameborder="0"
                        scrolling="no"
                        onload="resizeIframe(this);"></iframe>
                </div>
                <div class="googlemap">
                    <iframe allowfullscreen="" frameborder="0" height="450" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29793.996973529247!2d105.81945410109321!3d21.02269575409389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9bd9861ca1%3A0xe7887f7b72ca17a9!2zSMOgIE7hu5lpLCBIb8OgbiBLaeG6v20sIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1456991419208" style="border:0" width="600"></iframe>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
