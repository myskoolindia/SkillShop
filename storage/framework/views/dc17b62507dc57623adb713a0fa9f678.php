<section class="breadcrumb__area breadcrumb__bg" data-background="<?php echo e(asset(Cache::get('setting')?->breadcrumb_image)); ?>">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb__content">
                    <h3 class="title"><?php echo e($title); ?></h3>
                    <nav class="breadcrumb">
                        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span property="itemListElement" typeof="ListItem">
                                <a href="<?php echo e($loop->last ? 'javascript:;' : $link['url']); ?>"><?php echo e($link['text']); ?></a>
                            </span>
                            <?php if(!$loop->last): ?>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="breadcrumb__shape-wrap">
        <img src="<?php echo e(asset('frontend/img/others/breadcrumb_shape01.svg')); ?>" alt="img" class="alltuchtopdown">
        <img src="<?php echo e(asset('frontend/img/others/breadcrumb_shape02.svg')); ?>" alt="img" data-aos="fade-right"
            data-aos-delay="300">
        <img src="<?php echo e(asset('frontend/img/others/breadcrumb_shape03.svg')); ?>" alt="img" data-aos="fade-up"
            data-aos-delay="400">
        <img src="<?php echo e(asset('frontend/img/others/breadcrumb_shape04.svg')); ?>" alt="img" data-aos="fade-down-left"
            data-aos-delay="400">
        <img src="<?php echo e(asset('frontend/img/others/breadcrumb_shape05.svg')); ?>" alt="img" data-aos="fade-left"
            data-aos-delay="400">
    </div>
</section>
<?php /**PATH /var/www/html/pro/resources/views/components/frontend/breadcrumb.blade.php ENDPATH**/ ?>