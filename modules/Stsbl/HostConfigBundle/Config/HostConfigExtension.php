<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\Config;

use IServ\CrudBundle\Mapper\FormMapper;
use IServ\CrudBundle\Mapper\ShowMapper;
use IServ\HostBundle\Model\Host;
use IServ\HostBundle\Model\HostCollection;
use IServ\HostExtensionBundle\Crud\AbstractHostExtension;
use IServ\HostExtensionBundle\Crud\HostAdminExtensionInterface;
use Stsbl\HostConfigBundle\Config\Mapper\FieldMapperEntryFactory;
use Stsbl\HostConfigBundle\Entity\Config\HostConfig;
use Stsbl\HostConfigBundle\FieldDefinition\FieldDefinition;
use Stsbl\HostConfigBundle\FieldDefinition\FieldDefinitionProvider;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
    public const NAME = 'host-config';

    /**
     * @var FieldDefinitionProvider
     */
    private $definitionProvider;

    /**
     * @var HostConfigRepositoryInterface
     */
    private $configRepository;

    /**
     * @var FieldMapperEntryFactory
     */
    private $fieldMapperEntryFactory;

    public function __construct(
        FieldDefinitionProvider $definitionProvider,
        HostConfigRepositoryInterface $configRepository,
        FieldMapperEntryFactory $fieldMapperEntryFactory
    ) {
        $this->definitionProvider = $definitionProvider;
        $this->configRepository = $configRepository;
        $this->fieldMapperEntryFactory = $fieldMapperEntryFactory;
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
    public function configureFormFields(FormMapper $formMapper): void
    {
        foreach ($this->definitionProvider->provide()->all() as $fieldDefinition) {
            $field = $this->fieldMapperEntryFactory->createFormMapperEntry($fieldDefinition);
            $formMapper->add($field->getName(), $field->getType(), $field->getOptions());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureShowFields(ShowMapper $showMapper): void
    {
        foreach ($this->definitionProvider->provide()->all() as $fieldDefinition) {
            $field = $this->fieldMapperEntryFactory->createShowMapperEntry($fieldDefinition);
            $showMapper->add($field->getName(), $field->getType(), $field->getOptions());
        }
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
