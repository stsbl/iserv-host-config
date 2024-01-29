<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\FieldDefinition;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

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
final class FieldConfigurationLoader
{
    /**
     * @var Finder
     */
    private Finder $finder;

    /**
     * @param string[] $directories
     */
    public function __construct(
        private array $directories,
        ?Finder $finder = null,
    ) {
        $this->finder = $finder ?? new Finder();
    }

    /**
     * @return array<mixed>
     * @throws LoadException
     */
    public function load(): array
    {
        $yamlFiles = $this->finder
            ->in($this->directories)
            ->name('*.yaml')
            ->depth(0)
            ->getIterator()
        ;
        $parsed = [];

        foreach ($yamlFiles as $yamlFile) {
            try {
                $content = $yamlFile->getContents();
            } catch (\RuntimeException $e) {
                throw LoadException::couldNotRead($yamlFile->getPath(), $e->getMessage());
            }

            try {
                $parsed[] = Yaml::parse($content);
            } catch (ParseException $e) {
                throw LoadException::couldNotParse($yamlFile->getPath(), $e);
            }
        }

        return $parsed;
    }
}
