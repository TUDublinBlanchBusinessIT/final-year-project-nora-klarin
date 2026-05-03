<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::find(6);
Illuminate\Support\Facades\Auth::login($user);
$request = Illuminate\Http\Request::create('/child/wellbeing/start', 'POST', []);
$request->setUserResolver(fn() => $user);
$controller = new App\Http\Controllers\WellbeingCheckController(new App\Services\CheckQuestionSelector(), new App\Services\WellbeingCheckProcessor(new App\Services\WellbeingScoringService()));
$response = $controller->start($request);
var_dump($response->getData(true));
