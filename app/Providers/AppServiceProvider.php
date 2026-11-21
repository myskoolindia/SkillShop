<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Modules\GlobalSetting\app\Models\Setting;
use Modules\GlobalSetting\app\Models\CustomPagination;
use Modules\GlobalSetting\app\Models\MarketingSetting;
use Modules\GlobalSetting\app\Models\SeoSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            /** Cache settings */
            Cache::rememberForever('setting', fn () => (object) Setting::pluck('value', 'key')->all());
            Cache::rememberForever('marketing_setting', fn () => (object) MarketingSetting::pluck('value', 'key')->all());
            Cache::rememberForever('seo_setting', fn () => (object) SeoSetting::all()->groupBy('page_name')->mapWithKeys(function ($group, $pageName) {
                return [$pageName => $group->first()];
            }));
            if(Cache::has('setting')){
                set_wasabi_config();
                set_aws_config();
            }

            /** Auto-ensure Myskool International scenario data exists on deployment */
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && !\App\Models\User::where('email', 'myskool.international@myskool.club')->exists()) {
                (new \Database\Seeders\MyskoolInternationalDataSeeder())->run();
            }
        } catch (\Throwable $th) {
            logger($th);
        }

        /** Share setting to all views */
        View::composer('*', function ($view) {
            $setting = Cache::get('setting');
            $marketing_setting = Cache::get('marketing_setting');
            $seo_setting = Cache::get('seo_setting');
            $view->with(['setting' =>  $setting, 'marketing_setting' => $marketing_setting, 'seo_setting' => $seo_setting]);
        });

        // set timezone
        date_default_timezone_set(Cache::get('setting')?->timezone ?? config('app.timezone'));

        /**
         * Register custom blade directives
         * this can be used for permission or permissions check
         * this check will be perform on admin guard
         */
        $this->registerBladeDirectives();
        Paginator::useBootstrapFour();
    }

    protected function registerBladeDirectives()
    {
        Blade::directive('adminCan', function ($permission) {
            return "<?php if(auth()->guard('admin')->user()->can({$permission})): ?>";
        });

        Blade::directive('endadminCan', function () {
            return '<?php endif; ?>';
        });
    }
}
