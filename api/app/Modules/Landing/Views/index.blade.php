@extends('Landing::share.base')

@section('banner')
    <!-- Slide -->
    <div class="">
        <div class="slideslide">
            <div id="myCarousel" class="carousel slide">
                <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                    <li data-target="#myCarousel" data-slide-to="3"></li>
                    <li data-target="#myCarousel" data-slide-to="4"></li>
                    <li data-target="#myCarousel" data-slide-to="5"></li>
                </ol>
                <!-- Carousel items -->
                <div class="carousel-inner">
                    @foreach($listBanner as $index => $banner)
                        @if($index === 0)
                            <div class="active item">
                                <a href="" title="">
                                    <img src="{{config('app.media_url')}}{{$banner->image}}" alt="{{$banner->title}}" title="{{$banner->title}}" style="width: 100%;" />
                                </a>
                            </div>
                        @else
                            <div class="item">
                                <a href="" title="">
                                    <img src="{{config('app.media_url')}}{{$banner->image}}" alt="{{$banner->title}}" title="{{$banner->title}}" style="width: 100%;" />
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
                <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- Buoc thanh toan -->
<div class="b-cacbuocsudung">
    <ul>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a2.png" alt="{{config('app.app_name')}}" title="{{config('app.app_name')}}" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a1.png" alt="{{config('app.app_name')}}" title="{{config('app.app_name')}}" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a3.png" alt="{{config('app.app_name')}}" title="{{config('app.app_name')}}" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a4.png" alt="{{config('app.app_name')}}" title="{{config('app.app_name')}}" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a5.png" alt="{{config('app.app_name')}}" title="{{config('app.app_name')}}" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a6.png" alt="{{config('app.app_name')}}" title="{{config('app.app_name')}}" />
            </a>
        </li>
    </ul>
    <div class="clear-main"></div>
</div>

<!-- Main content -->

{!! $homeTaobao !!}

<div classs="clear-main"></div>
@endsection
