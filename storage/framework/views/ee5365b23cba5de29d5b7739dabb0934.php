<?php $__env->startSection('meta_title', 'Sign in to RoboQuest'); ?>
<?php $__env->startPush('styles'); ?>
<style>
.rq-signin{max-width:640px;margin:48px auto 80px;padding:36px;background:#fff;border:1px solid #e3def2;border-radius:24px;box-shadow:0 18px 55px #36246610}.rq-signin>small{color:#7053c5;letter-spacing:.14em;font-weight:700}.rq-signin h1{font-size:34px;margin:14px 0}.rq-signin p{color:#746a86;line-height:1.7}.rq-login-options{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:24px 0}.rq-login-option input{position:absolute;opacity:0}.rq-login-option span{display:block;padding:16px 8px;text-align:center;border:1px solid #e2ddee;border-radius:12px;color:#645873;cursor:pointer;font-weight:600}.rq-login-option input:checked+span{background:#efeaff;border-color:#6d4de3;color:#5735c4}.rq-login-option input:focus-visible+span{outline:3px solid #a693ec;outline-offset:3px}.rq-signin .form-grp{margin:20px 0}.rq-signin .form-grp label{display:block;margin-bottom:8px;font-weight:600}.rq-signin input[type=email],.rq-signin input[type=password]{width:100%;padding:14px;border:1px solid #dcd5ec;border-radius:10px}.rq-login-help{display:block;margin-top:18px;text-align:center}.rq-signin .alert{padding:14px;border-radius:10px;background:#fff1ef;color:#9c3434}@media(max-width:600px){.rq-signin{margin:24px 14px;padding:24px}.rq-signin h1{font-size:28px}.rq-login-option span{font-size:13px;padding:14px 5px}}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('contents'); ?>
<main class="rq-signin">
    <small>ROBOQUEST LEARNING LAB</small>
    <h1><?php echo e(__('Welcome to your learning space')); ?></h1>
    <p><?php echo e(__('Choose your account type and sign in to your school’s RoboQuest.')); ?></p>
    <?php if($errors->any()): ?><div class="alert" role="alert"><?php echo e($errors->first()); ?></div><?php endif; ?>
    <form method="POST" action="<?php echo e(route('roboquest.signin.store')); ?>">
        <?php echo csrf_field(); ?>
        <div class="rq-login-options" role="group" aria-label="<?php echo e(__('Sign-in options')); ?>">
            <?php $__currentLoopData = ['student'=>'Student', 'teacher'=>'Teacher', 'school'=>'School Admin']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="rq-login-option"><input type="radio" name="account_role" value="<?php echo e($value); ?>" <?php if(old('account_role', $role)===$value): echo 'checked'; endif; ?> required><span><?php echo e(__($label)); ?></span></label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="form-grp"><label for="rq-email"><?php echo e(__('Email address')); ?></label><input id="rq-email" name="login" type="email" autocomplete="username" value="<?php echo e(old('login')); ?>" required></div>
        <div class="form-grp"><label for="rq-password"><?php echo e(__('Password')); ?></label><input id="rq-password" name="password" type="password" autocomplete="current-password" required></div>
        <?php if($setting && $setting->recaptcha_status==='active'): ?><div class="g-recaptcha" data-sitekey="<?php echo e($setting->recaptcha_site_key); ?>"></div><?php endif; ?>
        <button type="submit" class="btn btn-two w-100"><?php echo e(__('Sign in to RoboQuest')); ?></button>
        <a class="rq-login-help" href="<?php echo e(route('password.request')); ?>"><?php echo e(__('Forgot your password?')); ?></a>
    </form>
    <p style="font-size:13px;margin-top:22px"><?php echo e(__('Use the account provided by your school. Your courses, assignments and permissions follow your verified account.')); ?></p>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/pro/resources/views/auth/roboquest-signin.blade.php ENDPATH**/ ?>