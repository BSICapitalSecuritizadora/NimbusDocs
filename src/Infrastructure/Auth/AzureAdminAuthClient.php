<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use TheNetworg\OAuth2\Client\Provider\Azure;

final class AzureAdminAuthClient
{
    private Azure $provider;

    private ?string $allowedDomain;

    /**
     * @param array{
     *   client_id: string,
     *   client_secret: string,
     *   redirect_uri: string,
     *   tenant_id: string,
     *   allowed_domain?: string|null
     * } $azureConfig
     */
    public function __construct(array $azureConfig)
    {
        $this->provider = new Azure([
            'clientId' => $azureConfig['client_id'] ?? '',
            'clientSecret' => $azureConfig['client_secret'] ?? '',
            'redirectUri' => $azureConfig['redirect_uri'] ?? '',
            'tenant' => $azureConfig['tenant_id'] ?? '',
            'defaultEndPointVersion' => '2.0',
        ]);

        $this->allowedDomain = $azureConfig['allowed_domain'] ?? null;
    }

    public function getProvider(): Azure
    {
        return $this->provider;
    }

    public function getAllowedDomain(): ?string
    {
        return $this->allowedDomain;
    }
}
