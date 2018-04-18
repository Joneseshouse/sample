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
        		<h1>{{$item->title}}</h1>
        		<br/>
        		<div>
        			{!! $item->content !!}
        		</div>
        	</div>
        </div>
    </div>
</div>

@endsection
