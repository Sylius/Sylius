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
interface SoftDeletableInterface
{
    /**
     * Is item deleted?
     *
     * @return bool
     */
    public function isDeleted();

    /**
     * Get the time of deletion.
     *
     * @return \DateTimeInterface
     */
    public function getDeletedAt();

    /**
     * Set deletion time.
     *
     * @param \DateTimeInterface $deletedAt
     */
    public function setDeletedAt(\DateTimeInterface $deletedAt = null);
}
