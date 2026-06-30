<?php

namespace App\Services\Sms;

use App\Services\AppConfigService;
use Illuminate\Support\Facades\Http;

class UnlimitedPortalProvider
{
    public function __construct(protected AppConfigService $config) {}

    protected function post(string $cmd, array $params = []): ?object
    {
        $payload = array_merge([
            'api_key' => $this->config->get('UNLIMITED_API_KEY', ''),
            'user' => $this->config->get('UNLIMITED_USER', ''),
            'cmd' => $cmd,
        ], $params);

        $response = Http::asForm()->timeout(30)->post('https://unlimitedportal.com/api_command.php', $payload);

        return $response->object();
    }

    public function listServices(): ?object
    {
        return $this->post('list_services');
    }

    public function requestNumber(string $service, array $extra = []): ?object
    {
        return $this->post('request', array_merge(['service' => $service], $extra));
    }

    public function readSms(string $orderId): ?object
    {
        return $this->post('read_sms', ['id' => $orderId]);
    }

    public function reject(string $orderId): ?object
    {
        return $this->post('reject', ['id' => $orderId]);
    }

    public function balance(): ?object
    {
        return $this->post('balance');
    }

    public function requestStatus(string $orderId): ?object
    {
        return $this->post('request_status', ['id' => $orderId]);
    }
}
