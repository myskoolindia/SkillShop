<?php $__env->startSection('meta_title', 'Error' . ' || ' . $setting->app_name); ?>

<?php $__env->startSection('contents'); ?>
    <!-- breadcrumb-area -->
    <?php if (isset($component)) { $__componentOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c = $attributes; } ?>
<?php $component = App\View\Components\Frontend\Breadcrumb::resolve(['title' => __('Error'),'links' => [
        ['url' => route('home'), 'text' => __('Home')],
        ['url' => route('checkout.index'), 'text' => __('Error')],
    ]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('frontend.breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Frontend\Breadcrumb::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c)): ?>
<?php $attributes = $__attributesOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c; ?>
<?php unset($__attributesOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c)): ?>
<?php $component = $__componentOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c; ?>
<?php unset($__componentOriginalcc52fffb9c918b8f3f4cf5faa6f3a53c); ?>
<?php endif; ?>
    <!-- breadcrumb-area-end -->

    <section class="error-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="error-wrap text-center">
                        <div class="error-img">
                            <img src="<?php echo e(asset('frontend/img/others/error_img.svg')); ?>" alt="img" class="injectable">
                        </div>
                        <div class="error-content">
                            <h2 class="title"><?php echo e(__('ERROR PAGE')); ?>! <span><?php echo e(__('Sorry This Page is Not Available')); ?>!</span></h2>
                            <div class="tg-button-wrap">
                                <a href="<?php echo e(url('/')); ?>" class="btn arrow-btn"><?php echo e(__('Go To Home Page')); ?> <img
                                        src="<?php echo e(asset('frontend/img/icons/right_arrow.svg')); ?>" alt="img" class="injectable"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/pro/resources/views/errors/404.blade.php ENDPATH**/ ?>