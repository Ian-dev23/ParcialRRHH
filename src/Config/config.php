<?php
/**
 * Configuración central de la aplicación.
 * En producción use variables de entorno en lugar de valores fijos.
 */

return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'dbname'  => 'parcial',
        'charset' => 'utf8mb4',
        'user'    => 'Ian',
        'pass'    => 'Septiembr323',
    ],
    'security' => [
        // Rutas a llaves y carpeta donde se guardan (fuera de /public)
        'keys_dir'     => __DIR__ . '/../../storage/keys',
        'private_key'  => __DIR__ . '/../../storage/keys/private.pem',
        'public_key'   => __DIR__ . '/../../storage/keys/public.pem',
    ],
];
