<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Web\FilterController;
use App\Http\Controllers\Web\WebController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/** Rotas para web */
Route::group(['as' => 'web.'], function(){

    /** Página inicial */
    Route::get('/', [WebController::class, 'home'])->name('home');

    /** Página de destaque */
    Route::get('/destaque', [WebController::class, 'spotlight'])->name('spotlight');

    /** Página de Locação */
    Route::get('/quero-alugar', [WebController::class, 'rent'])->name('rent');
    /** Página de Locação específica */
    Route::get('/quero-alugar/{slug}', [WebController::class, 'rentProperty'])->name('rentProperty');

    /** Página de Compra */
    Route::get('/quero-comprar', [WebController::class, 'buy'])->name('buy');
    /** Página de Compra específica */
    Route::get('/quero-comprar/{slug}', [WebController::class, 'buyProperty'])->name('buyProperty');

    /** Página de Filtro */
    Route::match(['post', 'get'],'/filtro', [WebController::class, 'filter'])->name('filter');

    /** Página de experiências */
    Route::get('/experiencias', [WebController::class, 'experience'])->name('experience');
    Route::get('/experiencias/{slug}', [WebController::class, 'experienceCategory'])->name('experienceCategory');

    /** Página inicial */
    Route::get('/contato', [WebController::class, 'contact'])->name('contact');
    /** Envio de Email */
    Route::post('/contato/sendEmail', [WebController::class, 'sendEmail'])->name('sendEmail');
    Route::get('/contato/sucesso', [WebController::class, 'sendEmailSuccess'])->name('sendEmailSuccess');

});

/** Rotas de Componente Filtro */
Route::group(['as' => 'component.'], function(){

    Route::post('/main-filter/search', [FilterController::class, 'search'])->name('main-filter.search');
    Route::post('/main-filter/category', [FilterController::class, 'category'])->name('main-filter.category');
    Route::post('/main-filter/type', [FilterController::class, 'type'])->name('main-filter.type');
    Route::post('/main-filter/neighborhood', [FilterController::class, 'neighborhood'])->name('main-filter.neighborhood');
    Route::post('/main-filter/bedrooms', [FilterController::class, 'bedrooms'])->name('main-filter.bedrooms');
    Route::post('/main-filter/suites', [FilterController::class, 'suites'])->name('main-filter.suites');
    Route::post('/main-filter/bathrooms', [FilterController::class, 'bathrooms'])->name('main-filter.bathrooms');
    Route::post('/main-filter/garage', [FilterController::class, 'garage'])->name('main-filter.garage');
    Route::post('/main-filter/price-base', [FilterController::class, 'priceBase'])->name('main-filter.priceBase');
    Route::post('/main-filter/price-limit', [FilterController::class, 'priceLimit'])->name('main-filter.priceLimit');

});

// /** ############# TESSTEEEE  ############# */
// Route::get('/teste', [FilterController::class, 'search'])->name('teste');

/** Rotas ganham o prefixo admin, ou seja, '/teste' => '/admin/teste */
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function(){

    /** Rotas de login */
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login'); // Show
    Route::post('/login', [AuthController::class, 'login'])->name('login.do'); // Action
    

    /** Rotas protegidas */
    Route::group(['middleware' => ['auth']], function(){
        /** Dashboard */
        Route::get('/home', [AuthController::class, 'home'])->name('home');

        /** Usuários */
        Route::get('/users/team', [UserController::class, 'team'])->name('users.team'); // Não faz parte da resource
        Route::resource('/users', UserController::class);

        /** Empresas */
        Route::resource('/companies', CompanyController::class);

        /** Imóveis */
        Route::post('/properties/image-set-cover', [PropertyController::class, 'imageSetCover'])->name('properties.imageSetCover');
        Route::delete('/properties/image-remove', [PropertyController::class, 'imageRemove'])->name('properties.imageRemove');
        Route::resource('/properties', PropertyController::class);

        /** Contrato */
        Route::post('/contracts/get-data-owner', [ContractController::class, 'getDataOwner'])->name('contracts.getDataOwner');
        Route::post('/contracts/get-data-acquirer', [ContractController::class, 'getDataAcquirer'])->name('contracts.getDataAcquirer');
        Route::post('/contracts/get-data-property', [ContractController::class, 'getDataProperty'])->name('contracts.getDataProperty');
        Route::resource('/contracts', ContractController::class);
    });  

    /** Rota de Logout */
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

});



