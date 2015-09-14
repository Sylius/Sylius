<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface TimestampableInterface
{
    /**
     * Get creation time.
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * Get the time of last update.
     *
     * @return \DateTimeInterface
     */
    public function getUpdatedAt();

    /**
     * Set creation time.
     *
     * @param \DateTimeInterface $createdAt
     */
    public function setCreatedAt(\DateTimeInterface $createdAt);

    /**
     * Set the time of last update.
     *
     * @param \DateTimeInterface $updatedAt
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt);
}
