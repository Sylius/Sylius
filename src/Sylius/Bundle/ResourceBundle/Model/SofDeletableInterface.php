<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Model;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface SoftDeletableInterface
{
    /**
     * Is item deleted?
     *
     * @return Boolean
     */
    public function isDeleted();

    /**
     * Get the time of deletion.
     *
     * @return \DateTime
     */
    public function getDeletedAt();

    /**
     * Set deletion time.
     *
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt);
}