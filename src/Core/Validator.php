<?php

namespace Itech\Core;

use InvalidArgumentException;

/**
 * Validaciones simples y específicas del dominio para colaboradores.
 * Lanza `InvalidArgumentException` cuando alguna regla no se cumple.
 */
class Validator
{
    public static function required(string $value, string $campo): void
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException("El campo {$campo} es obligatorio.");
        }
    }

    public static function email(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('El correo no tiene un formato válido.');
        }
    }

    public static function edad(int $edad): void
    {
        if ($edad < 18 || $edad > 100) {
            throw new InvalidArgumentException('La edad debe estar entre 18 y 100 años.');
        }
    }

    public static function opcionValida(string $valor, array $opciones, string $campo): void
    {
        if (!in_array($valor, $opciones, true)) {
            throw new InvalidArgumentException("El campo {$campo} no tiene una opción válida.");
        }
    }

    public static function idValido(int $id, string $campo): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("Debe seleccionar {$campo}.");
        }
    }

    public static function salario(float $salario): void
    {
        if ($salario <= 0) {
            throw new InvalidArgumentException('El salario debe ser mayor que cero.');
        }
    }

    public static function fechas(string $fechaInicio, ?string $fechaFin): void
    {
        self::required($fechaInicio, 'Fecha de inicio');

        if ($fechaFin !== null && $fechaFin < $fechaInicio) {
            throw new InvalidArgumentException('La fecha de fin no puede ser menor que la fecha de inicio.');
        }
    }

    public static function motivoBaja(?string $fechaFin, string $motivoBaja): void
    {
        if ($fechaFin !== null && trim($motivoBaja) === '') {
            throw new InvalidArgumentException('Debe escribir el motivo de baja si coloca una fecha de fin.');
        }
    }

    public static function colaboradorPerfil(array $data): void
    {
        self::required($data['identidad'] ?? '', 'Identidad');
        self::required($data['nombre'] ?? '', 'Nombre');
        self::required($data['apellido'] ?? '', 'Apellido');

        self::edad((int)($data['edad'] ?? 0));

        self::required($data['tipo_sangre'] ?? '', 'Tipo de sangre');

        self::opcionValida(
            $data['sexo'] ?? '',
            ['Masculino', 'Femenino'],
            'Sexo'
        );

        self::required($data['nacionalidad'] ?? '', 'Nacionalidad');

        self::opcionValida(
            $data['ruta'] ?? '',
            ['Panamá Este', 'Panamá Oeste', 'Panamá Norte'],
            'Ruta'
        );

        self::email($data['correo'] ?? '');

        self::required($data['celular'] ?? '', 'Celular');

        self::idValido((int)($data['id_ocupacion'] ?? 0), 'una ocupación');
        self::idValido((int)($data['id_planilla'] ?? 0), 'un tipo de planilla');

        self::salario((float)($data['salario'] ?? 0));

        self::fechas(
            $data['fecha_inicio'] ?? '',
            $data['fecha_fin'] ?? null
        );

        self::motivoBaja(
            $data['fecha_fin'] ?? null,
            $data['motivo_baja'] ?? ''
        );
    }
}