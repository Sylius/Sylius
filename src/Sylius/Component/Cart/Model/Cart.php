<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Model;

use Sylius\Component\Order\Model\Order;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Cart extends Order implements CartInterface
{
    /**
     * @var \DateTime
     */
    protected $expiresAt;

    public function __construct()
    {
        parent::__construct();

        $this->incrementExpiresAt();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return $this->getExpiresAt() < new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementExpiresAt()
    {
        $expiresAt = new \DateTime();
        $expiresAt->add(new \DateInterval('PT3H'));

        $this->expiresAt = $expiresAt;
    }
}
