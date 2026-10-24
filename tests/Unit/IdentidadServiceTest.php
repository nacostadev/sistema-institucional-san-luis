<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\IdentidadService;
use RuntimeException;
use Tests\TestCase;

class IdentidadServiceTest extends TestCase
{
    public function test_it_returns_a_deterministic_hmac_without_changing_dni_normalization(): void
    {
        config(['seguridad.pii_hmac_key' => 'test-key']);

        $service = new IdentidadService();

        $this->assertSame(
            hash_hmac('sha256', ' 12345678 ', 'test-key'),
            $service->hashDni(' 12345678 ')
        );
        $this->assertSame($service->hashDni('12345678'), $service->hashDni('12345678'));
        $this->assertSame(
            hash_hmac('sha256', 'user@example.test', 'test-key'),
            $service->hashCorreo('USER@example.test')
        );
        $this->assertSame('12345678', $service->descifrar($service->cifrar('12345678')));
    }

    public function test_it_throws_when_the_hmac_key_is_not_configured(): void
    {
        config(['seguridad.pii_hmac_key' => null]);

        $this->expectException(RuntimeException::class);

        (new IdentidadService())->hmac('12345678');
    }

    public function test_user_pii_attributes_are_encrypted_and_hashed_with_the_required_normalization(): void
    {
        config(['seguridad.pii_hmac_key' => 'test-key']);

        $user = new User();
        $user->dni = ' 12345678 ';
        $user->correo = 'USER@example.test';

        $attributes = $user->getAttributes();

        $this->assertSame(hash_hmac('sha256', ' 12345678 ', 'test-key'), $attributes['dni_hash']);
        $this->assertSame(hash_hmac('sha256', 'user@example.test', 'test-key'), $attributes['correo_hash']);
        $this->assertSame(' 12345678 ', $user->dni);
        $this->assertSame('user@example.test', $user->correo);
        $this->assertStringNotContainsString('12345678', $attributes['dni_cifrado']);
        $this->assertStringNotContainsString('USER@example.test', $attributes['correo_cifrado']);
    }
}
