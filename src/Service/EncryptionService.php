<?php

namespace Remyb98\DoctrineFieldEncrypt\Service;

readonly final class EncryptionService
{

    public function __construct(private string $secret)
    {
    }

    public function encrypt(string $data): string
    {
        return str_rot13($data);
    }

    public function decrypt(string $data): string
    {
        return str_rot13($data);
    }
}
