<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Model for orders.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Order implements OrderInterface
{
    /**
     * Id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Items in order.
     *
     * @var array
     */
    protected $items;

    /**
     * Whether order was confirmed.
     *
     * @var Boolean
     */
    protected $confirmed;

    /**
     * Confirmation token.
     *
     * @var string
     */
    protected $confirmationToken;

    protected $total;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Modification time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->confirmed = true;
        $this->total = 0;
        $this->generateConfirmationToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = (Boolean) $confirmed;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generateConfirmationToken()
    {
        if (null === $this->confirmationToken) {
            $bytes = false;
            if (function_exists('openssl_random_pseudo_bytes') && 0 !== stripos(PHP_OS, 'win')) {
                $bytes = openssl_random_pseudo_bytes(32, $strong);

                if (true !== $strong) {
                    $bytes = false;
                }
            }
            if (false === $bytes) {
                $bytes = hash('sha256', uniqid(mt_rand(), true), true);
            }

            $this->confirmationToken = base_convert(bin2hex($bytes), 16, 36);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(Collection $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearItems()
    {
        $this->items->clear();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(OrderItemInterface $item)
    {
        if (!$this->hasItem($item)) {
            $item->setOrder($this);
            $this->items->add($item);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(OrderItemInterface $item)
    {
        if ($this->hasItem($item)) {
            $item->setOrder(null);
            $this->items->removeElement($item);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(OrderItemInterface $item)
    {
        return $this->items->contains($item);
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    public function calculateTotal()
    {
        $total = 0;

        foreach ($this->items as $item) {
            $item->calculateTotal();

            $total += $item->getTotal();
        }

        $this->total = $total;

        return $this;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
