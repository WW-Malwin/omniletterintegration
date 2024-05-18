<?php
namespace OmniletterIntegration\Services;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class OmniletterService
{
    use Loggable;

    private $apiKey;
    private $apiSecret;
    private $baseUrl = 'https://apiv3.emailsys.net';
    private $plentyApi;

    public function __construct($apiKey, $apiSecret, $plentyApi)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->plentyApi = $plentyApi;
    }

    private function authenticate()
    {
        // Authentication logic
    }

    private function handleApiResponse($response)
    {
        if ($response->getStatusCode() != 200) {
            $this->getLogger(__METHOD__)->error('API Error', ['response' => $response->getBody()->getContents()]);
            throw new \Exception('API Error: ' . $response->getBody()->getContents());
        }
        return json_decode($response->getBody()->getContents(), true);
    }

    public function postToApi($endpoint, $data)
    {
        try {
            $response = $this->plentyApi->post($this->baseUrl . $endpoint, $data);
            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->error('Request Error', ['error' => $e->getMessage()]);
            throw new \Exception('Request Error: ' . $e->getMessage());
        }
    }

    public function getFromApi($endpoint)
    {
        try {
            $response = $this->plentyApi->get($this->baseUrl . $endpoint);
            return $this->handleApiResponse($response);
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->error('Request Error', ['error' => $e->getMessage()]);
            throw new \Exception('Request Error: ' . $e->getMessage());
        }
    }

    public function syncWithPlentymarkets()
    {
        $customers = $this->getPlentymarketsData();
        foreach ($customers as $customer) {
            $recipientData = $this->mapToOmniletterFormat($customer);
            $this->addRecipient($recipientData);
        }
    }

    private function getPlentymarketsData()
    {
        try {
            $response = $this->plentyApi->get('/rest/customers');
            return $this->handleApiResponse($response)['entries'];
        } catch (\Exception $e) {
            $this->getLogger(__METHOD__)->error('Plentymarkets Data Error', ['error' => $e->getMessage()]);
            throw new \Exception('Plentymarkets Data Error: ' . $e->getMessage());
        }
    }

    private function mapToOmniletterFormat($customer)
    {
        return [
            'email' => $customer['email'],
            'firstname' => $customer['firstName'],
            'lastname' => $customer['lastName']
        ];
    }

    public function addRecipient($recipientData)
    {
        return $this->postToApi('/recipients', $recipientData);
    }
}
?>
