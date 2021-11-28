<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\Opsi\Rpc;

use IServ\DeployBackendBundle\Rpc\Opsi\AbstractHandler;
use IServ\DeployBackendBundle\Security\Authentication\ClientToken;
use Stsbl\HostConfigBundle\Config\HostConfigRepositoryInterface;

final class HostConfigHandler extends AbstractHandler
{
    /**
     * {@inheritDoc}
     */
    protected $prefix = 'host_config_';

    public function __construct(
        private ClientToken $clientToken,
        private HostConfigRepositoryInterface $hostConfigRepository,
    ) {
    }

    public function host_config_get_value(string $key): ?string
    {
        $deployHost = $this->clientToken->getHost();

        if (null === $deployHost) {
            return null;
        }

        $host = $deployHost->getHost();

        return $this->hostConfigRepository->findAllForHost($host)->configurationForHost($host, $key)?->getValue();
    }
}
