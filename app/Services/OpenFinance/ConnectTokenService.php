<?php

namespace App\Services\OpenFinance;

use App\Models\User;

class ConnectTokenService
{
    public function __construct(private readonly PluggyClient $pluggyClient) {}

    public function generate(User $user): string
    {
        return $this->pluggyClient->createConnectToken((string) $user->id);
    }
}
