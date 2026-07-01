# ParcialRRHH
# Parcial #3

Sistema web desarrollado en PHP con arquitectura MVC para el registro de colaboradores, perfiles laborales e historial de cargos.

El sistema permite registrar datos personales, información de contacto, datos laborales, generar reportes, exportar a Excel y validar la integridad de datos sensibles mediante firmas digitales con OpenSSL.

---

## Descripción del Proyecto

Este proyecto corresponde al **Parcial #3**.

El objetivo principal es administrar colaboradores y sus perfiles laborales, permitiendo registrar la información personal y laboral de cada empleado, así como validar la integridad de datos sensibles.

El sistema registra:

- Datos personales del colaborador.
- Información de contacto.
- Ruta del colaborador.
- Ocupación o puesto.
- Tipo de planilla.
- Salario.
- Fecha de inicio del cargo.
- Fecha de finalización, si aplica.
- Estado activo del empleado.
- Estado activo del cargo.
- Motivo de baja.
- Firma digital de integridad de datos sensibles.

Además, permite verificar si los datos sensibles fueron alterados fuera del sistema.

---

## Tecnologías Utilizadas

- PHP 8.3
- MySQL / MariaDB
- WAMP Server
- Composer
- OpenSSL
- PhpSpreadsheet
- HTML5
- CSS3
- Programación Orientada a Objetos
- Arquitectura MVC

---

## Funcionalidades Principales

### Registro de Colaboradores

El formulario permite registrar los siguientes datos:

- Identidad o documento de identificación.
- Nombre.
- Apellido.
- Edad.
- Tipo de sangre.
- Sexo.
- Nacionalidad.
- Ruta del colaborador:
  - Panamá Este.
  - Panamá Oeste.
  - Panamá Norte.
- Correo.
- Celular.

---

## Perfil Laboral

El formulario también permite registrar datos laborales:

- Puesto u ocupación.
- Tipo de planilla:
  - Permanente.
  - Eventual.
  - Interino.
- Salario.
- Fecha de inicio.
- Fecha de fin, si aplica.
- Cargo activo.
- Empleado activo.
- Motivo de baja.

---

## Historial Laboral

El sistema permite manejar el historial laboral de cada colaborador.

Cuando un colaborador recibe una promoción o cambia de cargo, se crea un nuevo perfil laboral. El cargo anterior queda desactivado mediante el campo:

```sql
cargo_activo = 0
