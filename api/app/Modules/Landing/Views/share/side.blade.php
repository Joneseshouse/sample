<div class="span3">
    <div class="b-left-dm">
        <label>Order hàng</label>
    </div>
    <div class="b-nd-left">
        <div id="smoothmenu2" class="ddsmoothmenu-v">
            <ul>
                @foreach($listOrderhang as $orderHang)
                    <li>
                        <a
                            href="{!! route('Landing.detail', ['id' => $orderHang->id, 'slug' => $orderHang->slug]) !!}"
                            title="{{ $orderHang->title }}">
                            {{ $orderHang->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="clear-main"></div>
        </div>
    </div>
    <div class="b-left-dm">
        <label>Bài viết nổi bật</label>
    </div>
    <div class="padding-nd-left">
        @foreach($listImpressedArticle as $impressedArticle)
        <div class="b-new-mnleft">
            <a
                href="{!! route('Landing.detail', ['id' => $impressedArticle->id, 'slug' => $impressedArticle->slug]) !!}"
                title="{{$impressedArticle->title}}">
                @if($impressedArticle->thumbnail)
                <img
                    src="{{config('app.media_url')}}{{$impressedArticle->thumbnail}}"
                    title="{{$impressedArticle->title}}"
                    alt="{{$impressedArticle->title}}" />
                @else
                <img
                    src="{{config('app.static_url')}}images/default-image.jpg"
                    title="{{$impressedArticle->title}}"
                    alt="{{$impressedArticle->title}}" />
                @endif
            </a>
            <a href="/do-the-thao.html" title="{{$impressedArticle->title}}">
                <h3>{{$impressedArticle->title}}</h3>
            </a>
            <div class="clear-main"></div>
        </div>
        @endforeach
    </div>
</div>