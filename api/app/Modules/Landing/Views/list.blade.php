@extends('Landing::share.base')
@section('content')

<div class="row-fluid">
	@include('Landing::share.side')
	<div class="span9">
        <div class="b-bar-home">
            <a href="#" title="{{$title}}">
                <h1>{{$title}}</h1>
            </a>
        </div>
        <div class="border-content-home padding-ct">
        	<div class="box-ctbar">
        		@foreach($listItem as $item)
				<div class="box-run-new">
                    <div class="position-relative box-img-new">
                        <a href="{!! route('Landing.detail', ['id' => $item->id, 'slug' => $item->slug]) !!}" title="{{$item->title}}">

			                @if($item->thumbnail)
                            <img
                            	src="{{config('app.media_url')}}{{$item->thumbnail}}"
                            	title="{{$item->title}}"
                            	alt="{{$item->title}}" />
                            @else
                            <img
                            	src="{{config('app.static_url')}}images/default-image.jpg"
                            	title="{{$item->title}}"
                            	alt="{{$item->title}}" />
                            @endif
                        </a>
                    </div>
                    <!--end box-img-new-->
                    <a href="{!! route('Landing.detail', ['id' => $item->id, 'slug' => $item->slug]) !!}" title="{{$item->title}}">
                        <h3>{{$item->title}}</h3>
                    </a>
                    <div class="shortdes-new">
                    	{{$item->preview}}
                    </div>
                    <div class="xemchitiet">
                        <a href="{!! route('Landing.detail', ['id' => $item->id, 'slug' => $item->slug]) !!}" title="{{$item->title}}">
                    		Xem chi tiết.
                		</a>
                    </div>
                    <div class="clear-main"></div>
                </div>
                @endforeach
        	</div>
        </div>
    </div>
</div>

@endsection
