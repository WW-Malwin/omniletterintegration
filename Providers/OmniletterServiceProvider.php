<?php
namespace OmniletterIntegration\Providers;

use Plenty\Plugin\ServiceProvider;
use OmniletterIntegration\Services\OmniletterService;
use OmniletterIntegration\Services\EncryptionService;

class OmniletterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->getApplication()->bind(EncryptionService::class, function ($app) {
            $encryptionKey = getenv('ENCRYPTION_KEY');
            return new EncryptionService($encryptionKey);
        });

        $this->getApplication()->bind(OmniletterService::class, function ($app) {
            $config = pluginApp(\Plenty\Modules\Plugin\DataBase\Contracts\DataBase::class)->query('OmniletterIntegration\Models\OmniletterConfig')->first();
            $decryptedConfig = $config->getDecryptedConfig();
            return new OmniletterService($decryptedConfig['apiKey'], $decryptedConfig['apiSecret'], pluginApp(\Plenty\Plugin\Http\Request::class));
        });
    }
}
?>
