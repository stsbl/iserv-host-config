<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\FieldDefinition;

use Stsbl\HostConfigBundle\FieldDefinition\Collection\FieldDefinitionCollection;
use Stsbl\HostConfigBundle\FieldDefinition\Collection\FieldDefinitionCollectionBuilder;
use Symfony\Component\Config\Definition\Processor;

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
final class FieldConfigurationParser
{
    /**
     * @var FieldDefinitionConfiguration
     */
    private $configuration;

    /**
     * @var FieldDefinitionBuilder
     */
    private $builder;

    /**
     * @var Processor
     */
    private $processor;

    public function __construct(
        FieldDefinitionConfiguration $configuration,
        FieldDefinitionBuilder $builder,
        ?Processor $processor = null
    ) {
        $this->configuration = $configuration;
        $this->builder = $builder;
        $this->processor = $processor ?? new Processor();
    }

    /**
     * @param mixed $config
     * @return array<string,array<string,string>>
     */
    private function processConfigurationContent($config): array
    {
        return $this->processor->processConfiguration($this->configuration, [$config]);
    }

    /**
     * @return list<array<string,array<string,string>>>
     */
    private function tryLoadConfiguration(FieldConfigurationLoader $loader): array
    {
        $processed = [];

        try {
            /** @var mixed $contents */
            $contents = $loader->load();

            if (!\is_array($contents)) {
                throw new \InvalidArgumentException(
                    \sprintf('Loaded configuration content must be array. "%s" given.', \get_debug_type($contents))
                );
            }

            foreach ($contents as $content) {
                if (!\is_array($content)) {
                    throw new \InvalidArgumentException(
                        \sprintf(
                            'Loaded inner configuration content must be array. "%s" given.',
                            \get_debug_type($content)
                        )
                    );
                }

                if (!isset($content['host_config'])) {
                    throw new \InvalidArgumentException(
                        'Loaded inner configuration content does contain "host_config" key.'
                    );
                }

                $processed[] = $this->processConfigurationContent($content['host_config']);
            }
        } catch (LoadException $e) {
        } catch (\Throwable $e) {
        }

        return $processed;
    }

    public function parse(FieldConfigurationLoader $loader): FieldDefinitionCollection
    {
        $collectionBuilder = new FieldDefinitionCollectionBuilder();

        foreach ($this->tryLoadConfiguration($loader) as $config) {
            foreach ($config as $name => $item) {
                $collectionBuilder->add($this->builder->build($name, $item));
            }
        }

        return $collectionBuilder->build();
    }
}
