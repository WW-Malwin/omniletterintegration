<?php
namespace OmniletterIntegration\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;

class OmniletterConfig extends Model
{
    public $apiKey;
    public $apiSecret;

    public function __construct(array $data = [])
    {
        $encryptionService = pluginApp(\OmniletterIntegration\Services\EncryptionService::class);
        $this->apiKey = $encryptionService->encrypt($data['apiKey']);
        $this->apiSecret = $encryptionService->encrypt($data['apiSecret']);
    }

    public function getDecryptedConfig()
    {
        $encryptionService = pluginApp(\OmniletterIntegration\Services\EncryptionService::class);
        return [
            'apiKey' => $encryptionService->decrypt($this->apiKey),
            'apiSecret' => $encryptionService->decrypt($this->apiSecret)
        ];
    }
}
?>
