<?php

/**
 * Front Controller — punto de entrada único de la aplicación.
 */

declare(strict_types=1);

session_start();

// Cabeceras de seguridad básicas
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

require __DIR__ . '/../vendor/autoload.php';

use Itech\Controllers\ColabController;
use Itech\Controllers\ReporteController;
use Itech\Core\IntegritySigner;

// Genera las llaves OpenSSL si todavía no existen
IntegritySigner::ensureKeyPair();

$action = $_GET['action'] ?? 'form';

try {
    switch ($action) {
        case 'form':
            (new ColabController())->showForm();
            break;

        case 'store':
            (new ColabController())->store();
            break;

        case 'reporte':
            (new ReporteController())->index();
            break;

        case 'export':
            (new ReporteController())->exportExcel();
            break;

        default:
            http_response_code(404);
            echo '404 - Página no encontrada';
            break;
    }
} catch (\Throwable $e) {
    echo "<pre>";
    echo "ERROR:\n";
    echo $e->getMessage();
    echo "\n\n";
    echo "ARCHIVO:\n";
    echo $e->getFile() . " línea " . $e->getLine();
    echo "\n\n";
    echo "TRACE:\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}