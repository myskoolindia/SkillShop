<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CourseEnquiryController as AdminCourseEnquiryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Global\CloudStorageController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;


Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    /* Start admin auth route */
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('store-login', [AuthenticatedSessionController::class, 'store'])->name('store-login');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forget-password', [PasswordResetLinkController::class, 'custom_forget_password'])->name('forget-password');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'custom_reset_password_page'])->name('password.reset');
    Route::post('/reset-password-store/{token}', [NewPasswordController::class, 'custom_reset_password_store'])->name('password.reset-store');
    /* End admin auth route */

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'dashboard']);
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::controller(AdminProfileController::class)->group(function () {
            Route::get('edit-profile', 'edit_profile')->name('edit-profile');
            Route::put('profile-update', 'profile_update')->name('profile-update');
            Route::put('update-password', 'update_password')->name('update-password');
        });

        Route::get('role/assign', [RolesController::class, 'assignRoleView'])->name('role.assign');
        Route::post('role/assign/{id}', [RolesController::class, 'getAdminRoles'])->name('role.assign.admin');
        Route::put('role/assign', [RolesController::class, 'assignRoleUpdate'])->name('role.assign.update');
        Route::resource('/role', RolesController::class);
        Route::resource('/role', RolesController::class);

        /** Course Enquiries */
        Route::get('course-enquiries',                          [AdminCourseEnquiryController::class, 'index'])->name('course-enquiries');
        Route::get('course-enquiry/{id}',                       [AdminCourseEnquiryController::class, 'show'])->name('course-enquiry.show');
        Route::post('course-enquiry/{id}/send-mail',            [AdminCourseEnquiryController::class, 'sendMail'])->name('course-enquiry.send-mail');
        Route::post('course-enquiry/{id}/status',               [AdminCourseEnquiryController::class, 'updateStatus'])->name('course-enquiry.status');
        Route::delete('course-enquiry/{id}',                    [AdminCourseEnquiryController::class, 'destroy'])->name('course-enquiry.destroy');
        Route::post('course-enquiry/demo-scheduled',            [AdminCourseEnquiryController::class, 'demoScheduled'])->name('course-enquiry.demo-scheduled');

        // Quotation Routes
        Route::get('course-enquiry/{id}/quotation',             [AdminCourseEnquiryController::class, 'quotation'])->name('course-enquiry.quotation');
        Route::post('course-enquiry/{id}/quotation',            [AdminCourseEnquiryController::class, 'saveQuotation'])->name('course-enquiry.quotation.save');
        Route::get('course-enquiry/{id}/quotation/view',        [AdminCourseEnquiryController::class, 'viewQuotation'])->name('course-enquiry.quotation.view');

        // Proforma Invoice Routes
        Route::get('course-enquiry/{id}/proforma-invoice',      [AdminCourseEnquiryController::class, 'proformaInvoice'])->name('course-enquiry.proforma');
        Route::post('course-enquiry/{id}/proforma-invoice',     [AdminCourseEnquiryController::class, 'saveProformaInvoice'])->name('course-enquiry.proforma.save');
        Route::get('course-enquiry/{id}/proforma-invoice/view', [AdminCourseEnquiryController::class, 'viewProformaInvoice'])->name('course-enquiry.proforma.view');

        // Final Tax Invoice & Payment Collection Routes
        Route::get('course-enquiry/{id}/tax-invoice',           [AdminCourseEnquiryController::class, 'taxInvoice'])->name('course-enquiry.invoice');
        Route::post('course-enquiry/{id}/tax-invoice',          [AdminCourseEnquiryController::class, 'saveTaxInvoice'])->name('course-enquiry.invoice.save');
        Route::get('course-enquiry/{id}/tax-invoice/view',     [AdminCourseEnquiryController::class, 'viewTaxInvoice'])->name('course-enquiry.invoice.view');
        Route::post('course-enquiry/{id}/payment',              [AdminCourseEnquiryController::class, 'recordPayment'])->name('course-enquiry.payment.record');
        Route::delete('course-enquiry/{id}/payment/{payment_id}', [AdminCourseEnquiryController::class, 'deletePayment'])->name('course-enquiry.payment.delete');

        // Dynamic Bundle Items Loader
        Route::get('course-enquiry/{id}/default-items',         [AdminCourseEnquiryController::class, 'loadDefaultItems'])->name('course-enquiry.default-items');

        // ClubShop Product Integration for Line Items
        Route::get('clubshop/products/search',                  [AdminCourseEnquiryController::class, 'searchClubShopProducts'])->name('clubshop.products.search');
        Route::get('clubshop/products/catalog',                 [AdminCourseEnquiryController::class, 'getClubShopCatalog'])->name('clubshop.products.catalog');
    });
    Route::resource('admin', AdminController::class)->except('show');
    Route::put('admin-status/{id}', [AdminController::class, 'changeStatus'])->name('admin.status');
    // Settings routes
    Route::get('settings', [SettingController::class, 'settings'])->name('settings');
    Route::post('cloud/store', [CloudStorageController::class, 'store'])->name('cloud.store');
});
