<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;


Route::post('/get_customers_list', [CustomerController::class, 'getCustomersList']);
Route::get('/customers/export', [CustomerController::class, 'exportCustomersCsv']);


Route::get('/health', function () {
	return response()->json([
		'status' => 'ok',
		'time' => now()->toIso8601String(),
	]);
});
