<?php

namespace Itech\Core;

/**
 * Métodos de saneamiento para entrada de usuario (texto, email, números, fechas).
 * Devuelven valores ya normalizados/listos para validación y almacenamiento.
 */
class Sanitizer
{
    public static function text(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function email(string $value): string
    {
        $value = trim($value);
        $value = filter_var($value, FILTER_SANITIZE_EMAIL);

        return $value ?: '';
    }

    public static function title(string $value): string
    {
        $value = self::text($value);
        $value = mb_strtolower($value, 'UTF-8');

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public static function number(string|int|float $value): float
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    public static function int(string|int $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function date(?string $value): ?string
    {
        $value = trim((string)$value);

        if ($value === '') {
            return null;
        }

        return self::text($value);
    }
}