<?php $__env->startSection('banner'); ?>
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
                    <?php $__currentLoopData = $listBanner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($index === 0): ?>
                            <div class="active item">
                                <a href="" title="">
                                    <img src="<?php echo e(config('app.media_url')); ?><?php echo e($banner->image); ?>" alt="<?php echo e($banner->title); ?>" title="<?php echo e($banner->title); ?>" style="width: 100%;" />
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="item">
                                <a href="" title="">
                                    <img src="<?php echo e(config('app.media_url')); ?><?php echo e($banner->image); ?>" alt="<?php echo e($banner->title); ?>" title="<?php echo e($banner->title); ?>" style="width: 100%;" />
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Buoc thanh toan -->
<div class="b-cacbuocsudung">
    <ul>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a2.png" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a1.png" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a3.png" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a4.png" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a5.png" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" />
            </a>
        </li>
        <li>
            <a href="" title="">
                <img src="/public/static/images/a6.png" alt="<?php echo e(config('app.app_name')); ?>" title="<?php echo e(config('app.app_name')); ?>" />
            </a>
        </li>
    </ul>
    <div class="clear-main"></div>
</div>

<!-- Main content -->

<?php echo $homeTaobao; ?>


<div classs="clear-main"></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Landing::share.base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>