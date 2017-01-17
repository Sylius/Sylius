<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ChannelRepository extends EntityRepository implements ChannelRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByHostname($hostname)
    {
        return $this->findOneBy(['hostname' => $hostname]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCode($code)
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return $this->findBy(['name' => $name]);
    }
}
