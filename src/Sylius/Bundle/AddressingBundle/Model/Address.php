<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Model;

/**
 * Abstract model class for address.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
abstract class Address implements AddressInterface
{
    protected $id;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
        $this->incrementCreatedAt();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

  /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }
}
