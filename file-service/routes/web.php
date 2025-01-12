<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index', [
        'query' => request()->query(),
    ]);
})->name('index');

Route::get("/create", function() {
    return Inertia::render("Create");
})->name("create");