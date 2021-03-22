<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Crm\CrmAuthController;

Route::get('/crm/auth', [CrmAuthController::class, 'crmAuth']);