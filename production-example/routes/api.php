<?php

use App\Modules\Company\Managers\CompanyGetManager;
use App\Modules\Company\Managers\CompanyPutManager;
use App\Modules\Company\Managers\VerificationManager;
use App\Modules\UserAuth\Managers\AuthManager;
use App\Modules\UserAuth\Managers\ValidateManager;
use App\Modules\Vacancy\Controllers\VacancyDeleteController;
use App\Modules\Vacancy\Controllers\VacancyGetController;
use App\Modules\Vacancy\Controllers\VacancyIndexController;
use App\Modules\Vacancy\Controllers\VacancyPostController;
use App\Modules\Vacancy\Controllers\VacancyPutController;
use App\Modules\Vacancy\Managers\VacancyCompanyIndexManager;
use App\Modules\Vacancy\Managers\VacancyDeleteManager;
use App\Modules\Vacancy\Managers\VacancyGetManager;
use App\Modules\Vacancy\Managers\VacancyIndexManager;
use App\Modules\Vacancy\Managers\VacancyPostManager;
use App\Modules\Vacancy\Managers\VacancyPutManager;
use App\Modules\Vacancy\Managers\VacancyShowManager;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/',         AuthManager::class);
        Route::post('/validate', ValidateManager::class);
    });
    Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {
        Route::prefix('company')->group(function () {
            Route::post('/verification', VerificationManager::class);
            Route::get('/',     CompanyGetManager::class);
            Route::put('/',     CompanyPutManager::class);
            Route::prefix('/vacancies')->group(function () {
                Route::get('/',         VacancyCompanyIndexManager::class);
                Route::get('/{id}',     VacancyGetManager::class);
                Route::post('/',        VacancyPostManager::class);
                Route::put('/{id}',     VacancyPutManager::class);
                Route::delete('/{id}',  VacancyDeleteManager::class);
            });
        });
    });
    Route::prefix('vacancies')->group(function () {
        Route::get('/',                 VacancyIndexManager::class);
        Route::get('/{id}',             VacancyShowManager::class);
    });
    Route::prefix('webhook')->group(function () {
//        Route::prefix('cloudpayments')->group(function () {
//            Route::post('check', CloudPaymentsCheck::class);
//            Route::post('pay',   CloudPaymentsPay::class);
//        });
    });
});
