@extends('Landing::share.base')
@section('content')
<br/>
<section>
	<div class="container">
		<div class="row">

			<!-- main start -->
			<!-- ================ -->
			<div class="main col-md-8 space-bottom">

				<div class="alert alert-success" id="MessageSent">
					Chúng tôi đã nhận được thông tin liên hệ của bạn. Chúng tôi sẽ liên lạc với bạn sớm nhất có thể.
				</div>
				<div><a href="/" class="btn btn-primary">
					<span class="glyphicon glyphicon-home"></span> &nbsp;
					Trở về trang chủ
				</a></div>
			</div>
			<!-- main end -->

			<!-- sidebar start -->
			<!-- ================ -->
			<aside class="col-md-3 col-lg-offset-1">
				<div class="sidebar">
					<div class="side vertical-divider-left">
						<h3 class="title logo-font">
							<img src="/public/static/images/header-logo.png" style="max-width: 100%" alt="{{config('app.app_name')}}" title="{{config('app.app_name')}}"/>
						</h3>
						<div class="separator-2 mt-20"></div>
						<ul class="list">
							<li>
								<i class="fa fa-home pr-10"></i>
								{{ ConfigDb::get('contact-address') }}
							</li>
							<li>
								<i class="fa fa-mobile pr-10 pl-5"></i>
								<abbr title="Phone">M:</abbr>
								{{ ConfigDb::get('contact-phone') }}
							</li>
							<li>
								<i class="fa fa-envelope pr-10"></i>
								<a href="mailto:{{ ConfigDb::get('contact-email') }}">
									{{ ConfigDb::get('contact-email') }}
								</a>
							</li>
						</ul>
						<ul class="social-links circle small margin-clear clearfix animated-effect-1">
							<li class="twitter"><a target="_blank" href="http://www.twitter.com"><i class="fa fa-twitter"></i></a></li>
							<li class="skype"><a target="_blank" href="http://www.skype.com"><i class="fa fa-skype"></i></a></li>
							<li class="linkedin"><a target="_blank" href="http://www.linkedin.com"><i class="fa fa-linkedin"></i></a></li>
							<li class="googleplus"><a target="_blank" href="http://plus.google.com"><i class="fa fa-google-plus"></i></a></li>
							<li class="youtube"><a target="_blank" href="http://www.youtube.com"><i class="fa fa-youtube-play"></i></a></li>
							<li class="flickr"><a target="_blank" href="http://www.flickr.com"><i class="fa fa-flickr"></i></a></li>
							<li class="facebook"><a target="_blank" href="http://www.facebook.com"><i class="fa fa-facebook"></i></a></li>
						</ul>
						<div class="separator-2 mt-20 "></div>
						<a class="btn btn-gray collapsed map-show btn-animated" data-toggle="collapse" href="#collapseMap" aria-expanded="false" aria-controls="collapseMap">Hiện bản đồ <i class="fa fa-plus"></i></a>
					</div>
				</div>
			</aside>
			<!-- sidebar end -->
		</div>
	</div>
</section>
<!-- main-container end -->

@endsection
