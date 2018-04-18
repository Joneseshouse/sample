<!DOCTYPE html>
<head>
    <title><?php echo e($title); ?></title>
	<?php echo $__env->make('Landing::share.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</head>

<body>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>


    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5876e9f05e0a9c5f1bae52f7/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
    <!--End of Tawk.to Script-->

    <div id="loading_geturl"></div>

    <div class="chay2ben"></div>
    <div class="bg-head">
        <div class="main-width position-relative">
            <div class="row-fluid">
                <div class="span2">
                    <div class="b-logo">
                        <a href="/">
                            <img src="/public/static/images/logo.png" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" style="max-height: 85px;" />
                        </a>
                    </div>
                </div>
                <div class="span5">
                    <!-- <div class="b-search">
                        <form method="post" action="">
                            <div class="row-fluid">
                                <div class="span3">
                                    <select name="" class="span12">
                                        <option value="">Taobao.com</option>
                                        <option value="">1688.com</option>
                                    </select>
                                </div>
                                <div class="span9">
                                    <input type="text" name="tukhoa" class="span12" placeholder="Nhập từ khoá tìm kiếm." />
                                </div>
                            </div>
                        </form>
                    </div> -->
                </div>
                <div class="span5">
                    <div class="login-cart">
                        <ul class="fr-cr">
                            <li class="fr-li">
                                <div class="cart">
                                    <a href="<?php echo route('Landing.cart'); ?>">
                                        <i class="fa fa-cart-arrow-down"></i>Giỏ hàng
                                    </a>
                                </div>
                            </li>
                            <li class="fr-li">
                                <div class="padd-control">
                                    <a href="<?php echo e(config('app.extension_url')); ?>manage">
                                        <i class="fa fa-wrench"></i> Công cụ đặt hàng
                                    </a>
                                </div>
                            </li>
                            <li class="fr-li">
                                <div class="padd-control">
                                    <a href="<?php echo e(config('app.base_url')); ?>user">
                                        <i class="fa fa-lock"></i> Đăng nhập
                                    </a>
                                </div>
                            </li>
                            <li class="fr-li">
                                <div class="padd-control">
                                    <a href="<?php echo route('Landing.register'); ?>">
                                        <i class="fa fa-check-circle"></i> Đăng ký / liên hệ
                                    </a>
                                </div>
                            </li>
                        </ul>
                        <div class="clear-main"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end bg-head-->

    <!-- menu display -->
    <div class="bg-menu anmenu">
        <div class="main-width">
            <div id="smoothmenu1" class="navhor">
                <ul>
                    <li><a class="parent1" href="/" title="Trang chủ">Trang chủ</a></li>

                    <?php $__currentLoopData = $listCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <li>
                        <a class="parent1" href="<?php echo route('Landing.list', ['category_uid' => $category->uid]); ?>" title="<?php echo e($category->title); ?>">
                            <?php echo e($category->title); ?>

                        </a>
                        <ul>
                            <?php $__currentLoopData = $category->articles()->orderBy('order', 'desc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a
                                        href="<?php echo route('Landing.detail', ['id' => $article->id, 'slug' => $article->slug]); ?>"
                                        title="<?php echo e($article->title); ?>">
                                        <?php echo e($article->title); ?>

                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a class="parent1" href="<?php echo route('Landing.register'); ?>" title="Liên hệ">
                            Liên hệ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clear-main"></div>
    </div>
    <!--end bg-menu anmenu-->

    <?php $__env->startSection('banner'); ?>
    <?php echo $__env->yieldSection(); ?>
    <br/>
    <!-- Content -->
    <div class="main-width">
        <div class="">
        	<?php $__env->startSection('content'); ?>
			<?php echo $__env->yieldSection(); ?>
        </div>
    <!--end padding-content-->
    </div>
    <!-- End Content -->



    <!-- Footer -->
    <div class="bg-footer">
        <div class="main-width">
            <div class="row-fluid">
                <div class="span4">
                    <div class="title-footer">Thông tin liên hệ</div>
                    <div class="bg-ct-footer">
                        <p>
                            <strong>Văn phòng:</strong> <?php echo e(ConfigDb::get('contact-address')); ?>

                        </p>
                        <p>
                            <strong>Phone:</strong> <?php echo e(ConfigDb::get('contact-phone')); ?>

                        <p>
                        <p>
                            <strong>Email:</strong> <a href="mailto:<?php echo e(ConfigDb::get('contact-email')); ?>"><?php echo e(ConfigDb::get('contact-email')); ?></a>
                        </p>
                    </div>
                </div>
                <div class="span4">
                    <div class="title-footer">Facebook</div>
                    <div class="bg-ct-footer">
                        <div class="fb-page" data-href="https://www.facebook.com/facebook/" data-tabs="timeline" data-height="10" data-small-header="false" data-adapt-container-width="false" data-hide-cover="false" data-show-facepile="false"><blockquote cite="https://www.facebook.com/facebook/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/facebook/">Facebook</a></blockquote></div>
                    </div>
                </div>
                <div class="span4">
                    <div class="bandoft">
                        <iframe allowfullscreen="" frameborder="0" height="450" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29793.996973529247!2d105.81945410109321!3d21.02269575409389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9bd9861ca1%3A0xe7887f7b72ca17a9!2zSMOgIE7hu5lpLCBIb8OgbiBLaeG6v20sIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1456991419208" style="border:0" width="600"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear-main"></div>
    </div>

	<div class="fix-icon-right" id="fixedmenu">
        <div class="row-fluid">
            <ul class="unstyled">
                <li>
                    <a href="" title="Top"><img src="/public/static/images/gotop.gif" class="scrollup" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" /></a>
                </li>
            </ul>
        </div>
    </div>

	<?php echo $__env->make('Landing::share.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</body>
</html>
