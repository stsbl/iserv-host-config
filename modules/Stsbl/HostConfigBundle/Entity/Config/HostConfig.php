<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\Entity\Config;

use Doctrine\ORM\Mapping as ORM;
use IServ\HostBundle\Entity\Host;

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
 * @ORM\Entity(repositoryClass="Stsbl\HostConfigBundle\Config\HostConfigRepository")
 * @ORM\Table(name="host_config")
 *
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
class HostConfig
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     *
     * @var int|null
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="IServ\HostBundle\Entity\Host", fetch="EAGER")
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Host
     */
    private $host;

    /**
     * @ORM\Column(name="key", type="text", nullable=false)
     *
     * @var string
     */
    private $key;

    /**
     * @ORM\Column(name="value", type="text", nullable=true)
     *
     * @var string|null
     */
    private $value;

    public function __construct(Host $host, string $key, ?string $value)
    {
        $this->host = $host;
        $this->key = $key;
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHost(): Host
    {
        return $this->host;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
