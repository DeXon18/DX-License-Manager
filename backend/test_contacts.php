<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$contacts = App\Models\Contact::where('email', 'LIKE', '%lazpiur%')->get();
foreach($contacts as $c) { echo $c->client_id . " | " . $c->name . " | " . $c->email . "\n"; }
