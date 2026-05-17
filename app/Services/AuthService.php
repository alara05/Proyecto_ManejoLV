<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Sesion;
use App\Models\Usuario;
use App\Models\UsuarioRol;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class AuthService
{
    public function validarCredenciales(string $email, string $password): array
    {
        $usuario = Usuario::query()
            ->where('EMA_USU', $email)
            ->first();

        if (! $usuario) {
            throw new RuntimeException('Correo o contraseña incorrectos.');
        }

        if ($usuario->EST_USU !== 'ACTIVO') {
            throw new RuntimeException('El usuario no está activo.');
        }

        if (! $this->passwordValida($usuario, $password)) {
            throw new RuntimeException('Correo o contraseña incorrectos.');
        }

        $roles = $this->obtenerRolesUsuario($usuario->ID_USU);

        if (empty($roles)) {
            throw new RuntimeException('El usuario no tiene un rol asignado.');
        }

        return [
            'usuario' => $usuario,
            'roles' => $roles,
        ];
    }

    public function loginWeb(string $email, string $password, ?string $dispositivo = null): array
    {
        $datos = $this->validarCredenciales($email, $password);
        $token = $this->crearSesion($datos['usuario'], $dispositivo ?: 'WEB');

        return [
            'token' => $token,
            'usuario' => $this->formatearUsuario($datos['usuario'], $datos['roles']),
            'redirect' => $this->rutaDashboardWeb($datos['roles']),
        ];
    }

    public function loginMobile(string $email, string $password, ?string $dispositivo = null): array
    {
        $datos = $this->validarCredenciales($email, $password);

        if (! in_array('PASAJERO', $datos['roles'], true)) {
            throw new RuntimeException('Este acceso mobile es solo para pasajeros.');
        }

        $token = $this->crearSesion($datos['usuario'], $dispositivo ?: 'MOBILE');

        return [
            'token' => $token,
            'usuario' => $this->formatearUsuario($datos['usuario'], $datos['roles']),
            'redirect' => 'pages/home/dashboard.html',
        ];
    }

    public function registrarPasajero(array $datos, ?string $dispositivo = null): array
    {
        return DB::transaction(function () use ($datos, $dispositivo) {
            $rolPasajero = Rol::query()->firstOrCreate(
                ['NOM_ROL' => 'PASAJERO'],
                [
                    'DES_ROL' => 'Usuario final que compra boletos',
                    'EST_ROL' => 'ACTIVO',
                ]
            );

            if ($rolPasajero->EST_ROL !== 'ACTIVO') {
                $rolPasajero->EST_ROL = 'ACTIVO';
                $rolPasajero->save();
            }

            $usuario = Usuario::query()->create([
                'NOM_USU' => $datos['nombre'],
                'APE_USU' => $datos['apellido'] ?? null,
                'CED_USU' => $datos['cedula'],
                'EMA_USU' => $datos['email'],
                'TEL_USU' => $datos['telefono'] ?? null,
                'CLA_USU' => Hash::make($datos['password']),
                'EST_USU' => 'ACTIVO',
            ]);

            UsuarioRol::query()->create([
                'ID_USU_URO' => $usuario->ID_USU,
                'ID_ROL_URO' => $rolPasajero->ID_ROL,
                'EST_URO' => 'ACTIVO',
            ]);

            Cliente::query()->create([
                'ID_USU_CLI' => $usuario->ID_USU,
                'NOM_CLI' => $datos['nombre'],
                'APE_CLI' => $datos['apellido'] ?? null,
                'CED_CLI' => $datos['cedula'],
                'EMA_CLI' => $datos['email'],
                'TEL_CLI' => $datos['telefono'] ?? null,
                'EST_CLI' => 'ACTIVO',
            ]);

            $roles = ['PASAJERO'];
            $token = $this->crearSesion($usuario, $dispositivo ?: 'MOBILE');

            return [
                'token' => $token,
                'usuario' => $this->formatearUsuario($usuario, $roles),
                'redirect' => 'pages/home/dashboard.html',
            ];
        });
    }

    public function cerrarSesion(?string $token): void
    {
        if (! $token) {
            return;
        }

        Sesion::query()->where('TOK_SES', $token)->delete();
    }

    private function passwordValida(Usuario $usuario, string $password): bool
    {
        $hashActual = (string) $usuario->CLA_USU;

        if (Hash::check($password, $hashActual)) {
            return true;
        }

        if (hash_get_info($hashActual)['algo'] === 0 && hash_equals($hashActual, $password)) {
            $usuario->CLA_USU = Hash::make($password);
            $usuario->save();

            return true;
        }

        return false;
    }

    private function obtenerRolesUsuario(int $idUsuario): array
    {
        return Rol::query()
            ->join('USUARIO_ROL', 'USUARIO_ROL.ID_ROL_URO', '=', 'ROL.ID_ROL')
            ->where('USUARIO_ROL.ID_USU_URO', $idUsuario)
            ->where('USUARIO_ROL.EST_URO', 'ACTIVO')
            ->where('ROL.EST_ROL', 'ACTIVO')
            ->pluck('ROL.NOM_ROL')
            ->values()
            ->toArray();
    }

    private function crearSesion(Usuario $usuario, string $dispositivo): string
    {
        $token = Str::random(80);

        Sesion::query()->create([
            'ID_USU_SES' => $usuario->ID_USU,
            'TOK_SES' => $token,
            'DIS_SES' => Str::limit($dispositivo, 150, ''),
            'EXP_SES' => Carbon::now()->addHours(8),
        ]);

        return $token;
    }

    private function formatearUsuario(Usuario $usuario, array $roles): array
    {
        return [
            'id' => $usuario->ID_USU,
            'nombre' => trim($usuario->NOM_USU . ' ' . ($usuario->APE_USU ?? '')),
            'nombres' => $usuario->NOM_USU,
            'apellidos' => $usuario->APE_USU,
            'cedula' => $usuario->CED_USU,
            'email' => $usuario->EMA_USU,
            'telefono' => $usuario->TEL_USU,
            'roles' => $roles,
            'rol_principal' => $roles[0] ?? null,
        ];
    }

    private function rutaDashboardWeb(array $roles): string
    {
        if (in_array('ADMIN', $roles, true)) {
            return '/dashboard';
        }

        if (in_array('USUARIO_COOPERATIVA', $roles, true)) {
            return '/cooperativa/dashboard';
        }

        if (in_array('OFICINISTA', $roles, true)) {
            return '/oficinista/dashboard';
        }

        if (in_array('CHOFER', $roles, true) || in_array('AYUDANTE', $roles, true)) {
            return '/personal-bus/dashboard';
        }

        if (in_array('PASAJERO', $roles, true)) {
            return '/pasajero/dashboard';
        }

        return '/dashboard';
    }
}
