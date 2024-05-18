<?php
namespace OmniletterIntegration\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Templates\Twig;
use OmniletterIntegration\Services\OmniletterService;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Log\Loggable;
use OmniletterIntegration\Models\OmniletterConfig;

class MainController extends Controller
{
    use Loggable;

    private $omniletterService;
    private $database;

    public function __construct(OmniletterService $omniletterService, DataBase $database)
    {
        $this->omniletterService = $omniletterService;
        $this->database = $database;
    }

    public function showConfig(Twig $twig)
    {
        $config = $this->database->query(OmniletterConfig::class)->first();
        $decryptedConfig = $config ? $config->getDecryptedConfig() : ['apiKey' => '', 'apiSecret' => ''];
        return $twig->render('OmniletterIntegration::config', ['config' => $decryptedConfig]);
    }

    public function saveConfig(Request $request)
    {
        $data = $request->all();
        $config = $this->database->query(OmniletterConfig::class)->first();

        if ($config) {
            $config->update($data);
        } else {
            $this->database->save(new OmniletterConfig($data));
        }

        return response()->json(['status' => 'success']);
    }

    public function syncCustomers()
    {
        try {
            $this->omniletterService->syncWithPlentymarkets();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->error('Sync failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function showLogs(Twig $twig)
    {
        $logs = $this->database->query('Plenty\Modules\Plugin\Log\Models\Log')->all();
        return $twig->render('OmniletterIntegration::logs', ['logs' => $logs]);
    }
}
?>
