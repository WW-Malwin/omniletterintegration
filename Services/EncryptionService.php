<?php
namespace OmniletterIntegration\Services;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class EncryptionService
{
    private $encryptionKey;

    public function __construct($keyString)
    {
        $this->encryptionKey = Key::loadFromAsciiSafeString($keyString);
    }

    public function encrypt($data)
    {
        return Crypto::encrypt($data, $this->encryptionKey);
    }

    public function decrypt($data)
    {
        return Crypto::decrypt($data, $this->encryptionKey);
    }
}
?>
