<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Versión del Sistema
    |--------------------------------------------------------------------------
    |
    | Aquí se define la versión global de la plataforma, que se mostrará en
    | el footer, la pantalla de login y cualquier otra interfaz que lo requiera.
    |
    */
    'version' => (function() {
        $path = base_path('../management/CHANGELOG.md');
        if (file_exists($path) && preg_match('/^\>\s*\*\*Version:\*\*\s*(v[\d\.]+)/m', file_get_contents($path), $m)) {
            return $m[1];
        }
        return 'v3.0.0';
    })(),
];
