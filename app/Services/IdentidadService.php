<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class IdentidadService
{
    public function hmac(string $identificador): string
    {
        $key = config('seguridad.pii_hmac_key');

        if (! is_string($key) || $key === '') {
            throw new RuntimeException('La clave PII_HMAC_KEY no está configurada.');
        }

        return hash_hmac('sha256', $identificador, $key);
    }

    public function hashDni(string $dni): string
    {
        return $this->hmac($dni);
    }

    public function hashCorreo(string $correo): string
    {
        return $this->hmac(strtolower($correo));
    }

    public function cifrar(string $valor): string
    {
        return Crypt::encryptString($valor);
    }

    public function descifrar(string $valorCifrado): string
    {
        return Crypt::decryptString($valorCifrado);
    }
}
