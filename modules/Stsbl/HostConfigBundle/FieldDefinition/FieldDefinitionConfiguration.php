<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\FieldDefinition;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
final class FieldDefinitionConfiguration implements ConfigurationInterface
{
    public const ROOT_NODE = 'host_config';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder(self::ROOT_NODE);

        /**
         * TreeBuilder is not type-safe.
         * @psalm-suppress MixedMethodCall, PossiblyUndefinedMethod, PossiblyNullReference
         */
        $builder->getRootNode()
            ->ignoreExtraKeys()
            ->children()
                ->arrayNode('fields')
                    ->normalizeKeys(false)
                    ->ignoreExtraKeys()
                    ->info('Field names as keys')
                    ->useAttributeAsKey('name')->example('my_awesome_config_value')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('description')
                                ->isRequired()
                                ->example('The awesome configuration value')
                                ->info('The human readable description of this configuration')
                            ->end()
                            ->scalarNode('help_text')
                                ->example('The awesome help text')
                                ->info('The human readable help text of this configuration')
                            ->end()
                            ->enumNode('type')
                                ->isRequired()
                                ->defaultValue('text')
                                ->example('text')
                                ->info('The field type of this configuration')
                                ->values(['text', 'password'])
                            ->end()
                            ->scalarNode('group')
                                ->example('My module')
                                ->info('The group where to place the field on host show page')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
