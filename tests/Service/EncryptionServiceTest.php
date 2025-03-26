<?php

namespace Service;

use Remyb98\DoctrineFieldEncrypt\Service\EncryptionService;
use PHPUnit\Framework\TestCase;

class EncryptionServiceTest extends TestCase
{
    public function testEncrypt()
    {
        $service = new EncryptionService('secret');
        $clearData = 'sensitive data';
        $encryptedData = $service->encrypt($clearData);
        $this->assertNotEquals($clearData, $encryptedData);
        $this->assertEquals(str_rot13($clearData), $encryptedData);
    }

    public function testDecrypt()
    {
        $service = new EncryptionService('secret');
        $encryptedData = 'frafvgvir qngn';
        $clearData = $service->decrypt($encryptedData);
        $this->assertNotEquals($encryptedData, $clearData);
        $this->assertEquals(str_rot13($encryptedData), $clearData);
    }
}
