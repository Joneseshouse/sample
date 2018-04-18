@extends('Landing::share.base')
@section('content')

<div class="row-fluid">
	@include('Landing::share.side')
	<div class="span9">
		<div class="b-bar-home uppercase">
	        <h1>Giỏ hàng</h1>
	    </div>

		<div class="border-content-home padding-ct">
            <div style="margin: 5px; color: #000;">

                @if($status !== 'new')
                    @if($status === 'success')
                        <div class="alert alert-success">{{ $message }}</div>
                    @else
                        <div class="alert alert-error">{{ $message }}</div>
                    @endif
                @endif
                <div>
                    <iframe
						src="{{config('app.base_url')}}user/cart"
						style="width: 100%; height: 500px"
						frameborder="0"
						scrolling="no"
						onload="resizeIframe(this);"></iframe>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
