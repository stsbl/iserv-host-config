<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\FieldDefinition\Collection;

use Stsbl\HostConfigBundle\FieldDefinition\FieldDefinition;

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
final class FieldDefinitionCollection
{
    /**
     * @var array<string,FieldDefinition>
     */
    private $fieldDefinitions = [];

    /**
     * @param FieldDefinition[] $fieldDefinitions
     */
    public function __construct(array $fieldDefinitions)
    {
        foreach ($fieldDefinitions as $fieldDefinition) {
            $this->fieldDefinitions[$fieldDefinition->getName()] = $fieldDefinition;
        }
    }

    /**
     * @return FieldDefinition[]
     */
    public function all(): array
    {
        return \array_values($this->fieldDefinitions);
    }

    public function get(string $name): ?FieldDefinition
    {
        return $this->fieldDefinitions[$name] ?? null;
    }
}
