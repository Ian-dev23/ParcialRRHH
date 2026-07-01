<?php

namespace Itech\Core;

/**
 * Firma y verificación de integridad de registros usando OpenSSL (RSA-SHA256).
 *
 * Se construye una cadena canónica con campos críticos y se firma/verifica
 * contra las llaves RSA definidas en la configuración.
 */
class IntegritySigner
{
    /**
     * Genera el par de llaves RSA si todavía no existen.
     */
    public static function ensureKeyPair(): void
    {
        $config = require __DIR__ . '/../Config/config.php';
        $privateKeyPath = $config['security']['private_key'];
        $publicKeyPath  = $config['security']['public_key'];

        if (file_exists($privateKeyPath) && file_exists($publicKeyPath)) {
            return;
        }

        $dir = dirname($privateKeyPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        if (!extension_loaded('openssl')) {
            throw new \RuntimeException(
                'La extensión OpenSSL no está habilitada en PHP. En WAMP: menú WAMP > PHP > ' .
                'Extensiones PHP > marca "php_openssl", y reinicia todos los servicios.'
            );
        }

        // Configuración opcional de openssl.cnf para entornos como WAMP.
        $opensslConfigPath = self::findOpensslConfig();

        $keyConfig = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        if ($opensslConfigPath !== null) {
            $keyConfig['config'] = $opensslConfigPath;
        }

        $res = openssl_pkey_new($keyConfig);

        if ($res === false) {
            echo "<pre>";

            while ($msg = openssl_error_string()) {
                 echo $msg . PHP_EOL;
             }

            echo "</pre>";
            exit;
         }

        openssl_pkey_export($res, $privateKeyOut);
        file_put_contents($privateKeyPath, $privateKeyOut);
        chmod($privateKeyPath, 0600);

        $details = openssl_pkey_get_details($res);
        file_put_contents($publicKeyPath, $details['key']);
    }

    private static function findOpensslConfig(): ?string
    {
            // 1. Respeta php.ini si está definido.
        $iniValue = ini_get('openssl.cnf');
        if ($iniValue && file_exists($iniValue)) {
            return $iniValue;
        }

        // 2. Variable de entorno OPENSSL_CONF.
        $envValue = getenv('OPENSSL_CONF');
        if ($envValue && file_exists($envValue)) {
            return $envValue;
        }

        // 3. Búsqueda en rutas típicas de instalación local (WAMP/Apache/PHP).
        $candidates = glob('C:/wamp64/bin/apache/apache*/conf/openssl.cnf') ?: [];
        $candidates = array_merge($candidates, glob('C:/wamp64/bin/php/php*/extras/ssl/openssl.cnf') ?: []);
        $candidates = array_merge($candidates, glob('C:/wamp/bin/apache/apache*/conf/openssl.cnf') ?: []);

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Construye la cadena canónica a firmar a partir de los campos críticos.
     */
    private static function buildPayload(array $data): string
    {
        return implode('|', [
            trim((string)($data['salario'] ?? '')),
            trim((string)($data['id_empleado'] ?? '')),
            trim((string)($data['id_planilla'] ?? '')),
            trim((string)($data['id_ocupacion'] ?? '')),
            trim((string)($data['fecha_inicio'] ?? '')),
        ]);
    }

    /**
     * Firma los datos críticos y devuelve la firma en base64.
     */
    public static function sign(array $data): string
    {
        $config = require __DIR__ . '/../Config/config.php';
        $privateKeyPem = file_get_contents($config['security']['private_key']);
        $privateKey = openssl_pkey_get_private($privateKeyPem);

        $payload = self::buildPayload($data);
        $signature = '';
        openssl_sign($payload, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    /**
     * Verifica si la firma almacenada corresponde a los datos actuales.
     */
    public static function verify(array $data, string $signatureBase64): bool
    {
        $config = require __DIR__ . '/../Config/config.php';
        $publicKeyPem = file_get_contents($config['security']['public_key']);
        $publicKey = openssl_pkey_get_public($publicKeyPem);

        $payload = self::buildPayload($data);
        $signature = base64_decode($signatureBase64);

        $result = openssl_verify($payload, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }
}
