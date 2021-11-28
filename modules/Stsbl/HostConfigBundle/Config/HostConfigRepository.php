<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\Config;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use IServ\HostBundle\Entity\Host;
use IServ\HostBundle\Model\Host as HostModel;
use IServ\HostBundle\Model\HostCollection;
use Stsbl\HostConfigBundle\Config\Collection\HostConfigCollection;
use Stsbl\HostConfigBundle\Config\Collection\HostConfigCollectionBuilder;
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
 * @extends ServiceEntityRepository<HostConfig>
 *
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
final class HostConfigRepository extends ServiceEntityRepository implements HostConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HostConfig::class);
    }

    /**
     * {@inheritDoc}
     */
    public function findAllForHost(Host $host): HostConfigCollection
    {
        $configs = $this->findBy([
            'host' => $host,
        ]);

        $builder = new HostConfigCollectionBuilder();

        foreach ($configs as $config) {
            $builder->add($config);
        }

        return $builder->build();
    }

    /**
     * {@inheritDoc}
     */
    public function findAllForAllHosts(HostCollection $hosts): HostConfigCollection
    {
        $builder = new HostConfigCollectionBuilder();

        $qb = $this->createQueryBuilder('c');

        $qb
            ->select('c')
            ->where($qb->expr()->in('c.host', ':hosts'))
        ;

        $qb->setParameter('hosts', \array_map(
            static function (HostModel $host): Host {
                return $host->getEntity();
            },
            $hosts->getHosts()
        ));

        foreach ($qb->getQuery()->toIterable() as $hostConfig) {
            $builder->add($hostConfig);
        }

        return $builder->build();
    }

    /**
     * {@inheritDoc}
     */
    public function save(HostConfig $hostConfig): void
    {
        try {
            $this->getEntityManager()->persist($hostConfig);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            throw new \RuntimeException('Could not save host config.', 0, $e);
        }
    }
}
