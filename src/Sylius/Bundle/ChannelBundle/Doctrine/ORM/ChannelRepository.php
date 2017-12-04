<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ChannelBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

class ChannelRepository extends EntityRepository implements ChannelRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByHostname(string $hostname): ?ChannelInterface
    {
        return $this->findOneBy(['hostname' => $hostname]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCode(string $code): ?ChannelInterface
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $name): iterable
    {
        return $this->findBy(['name' => $name]);
    }
}
