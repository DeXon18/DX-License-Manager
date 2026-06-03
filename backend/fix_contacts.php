<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$contacts = App\Models\Contact::where('client_id', 4)
    ->whereIn('email', [
        'billie.wang@lazpiur.cn',
        'river.shan@lazpiur.cn',
        'a.fernandez@lazpiur.com',
        'david.yuan@lazpiur.cn',
        'informatica@lazpiur.com'
    ])->get();

foreach($contacts as $c) {
    $c->client_id = 270;
    $c->save();
    echo "Movido: " . $c->name . " al cliente 270\n";
}
