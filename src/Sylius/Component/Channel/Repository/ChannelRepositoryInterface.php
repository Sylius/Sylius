<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * Repository interface for channels.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelRepositoryInterface
{
    /**
     * Find channel best matching given hostname.
     *
     * @param string $hostname
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findMatchingHostname($hostname);

    /**
     * @return ChannelInterface
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findDefault();
}
