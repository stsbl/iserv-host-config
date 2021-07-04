<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\Config;

use IServ\HostBundle\Model\Host;
use IServ\HostBundle\Model\HostCollection;
use IServ\HostExtensionBundle\Crud\AbstractHostExtension;
use IServ\HostExtensionBundle\Crud\HostAdminExtensionInterface;
use Stsbl\HostConfigBundle\Entity\Config\HostConfig;

/*
 * The MIT License
 *
 * Copyright 2021 Felix Jacobi.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
final class HostConfigExtension extends AbstractHostExtension implements HostAdminExtensionInterface
{
    private const NAME = 'host-config';

    /**
     * @var HostConfigRepositoryInterface
     */
    private $configRepository;

    public function __construct(HostConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function extendModel(Host $host): void
    {
        $hostConfigs = $this->configRepository->findAllForHost($host->getEntity());

        self::incorporateHostConfig($hostConfigs, $host);
    }

    /**
     * {@inheritDoc}
     */
    public function extendModels(HostCollection $hosts): void
    {
        $hostConfigs = $this->configRepository->findAllForAllHosts($hosts);

        foreach ($hosts->getHosts() as $host) {
            self::incorporateHostConfig($hostConfigs->forHost($host), $host);
        }
    }

    /**
     * @param HostConfig[] $hostConfigs
     */
    private static function incorporateHostConfig(array $hostConfigs, Host $host): void
    {
        foreach ($hostConfigs as $hostConfig) {
            $host->setExtensionValue(self::NAME, $hostConfig->getKey(), $hostConfig->getValue());
        }
    }
}
