<?php

Route::post('webhooks/paddle', '\Kanuu\Laravel\HandlePaddleWebhook')->name('webhooks.paddle');
Route::get('kanuu/{identifier}', '\Kanuu\Laravel\RedirectToKanuu')->name('kanuu.redirect');
