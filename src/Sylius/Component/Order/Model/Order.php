<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Order implements OrderInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $completedAt;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $additionalInformation;

    /**
     * @var Collection|OrderItemInterface[]
     */
    protected $items;

    /**
     * @var int
     */
    protected $itemsTotal = 0;

    /**
     * @var Collection|AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var Collection|CommentInterface[]
     */
    protected $comments;

    /**
     * @var Collection|IdentityInterface[]
     */
    protected $identities;

    /**
     * @var int
     */
    protected $adjustmentsTotal = 0;

    /**
     * Calculated total.
     * Items total + adjustments total.
     *
     * @var int
     */
    protected $total = 0;

    /**
     * @var string
     */
    protected $state = OrderInterface::STATE_CART;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->adjustments = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->identities = new ArrayCollection();
        $this->createdAt = new \DateTime();
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
    public function isCompleted()
    {
        return null !== $this->completedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function complete()
    {
        $this->completedAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCompletedAt(\DateTime $completedAt = null)
    {
        $this->completedAt = $completedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * {@inheritdoc}
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * {@inheritdoc}
     */
    public function getSequenceType()
    {
        return 'order';
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
    public function clearItems()
    {
        $this->items->clear();

        $this->recalculateItemsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return $this->items->count();
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(OrderItemInterface $item)
    {
        if ($this->hasItem($item)) {
            return;
        }

        $this->itemsTotal += $item->getTotal();
        $this->items->add($item);
        $item->setOrder($this);

        $this->recalculateTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(OrderItemInterface $item)
    {
        if ($this->hasItem($item)) {
            $this->items->removeElement($item);
            $this->itemsTotal -= $item->getTotal();
            $this->recalculateTotal();
            $item->setOrder(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(OrderItemInterface $item)
    {
        return $this->items->contains($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsTotal()
    {
        return $this->itemsTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateItemsTotal()
    {
        $this->itemsTotal = 0;
        foreach ($this->items as $item) {
            $this->itemsTotal += $item->getTotal();
        }

        $this->recalculateTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * {@inheritdoc}
     */
    public function addComment(CommentInterface $comment)
    {
        if (!$this->comments->contains($comment)) {
            $comment->setOrder($this);
            $this->comments->add($comment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeComment(CommentInterface $comment)
    {
        if ($this->comments->contains($comment)) {
            $comment->setOrder(null);
            $this->comments->removeElement($comment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalQuantity()
    {
        $quantity = 0;

        foreach ($this->items as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->items->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addIdentity(IdentityInterface $identity)
    {
        if (!$this->hasIdentity($identity)) {
            $this->identities->add($identity);

            $identity->setOrder($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasIdentity(IdentityInterface $identity)
    {
        return $this->identities->contains($identity);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * {@inheritdoc}
     */
    public function removeIdentity(IdentityInterface $identity)
    {
        if ($this->hasIdentity($identity)) {
            $identity->setOrder(null);
            $this->identities->removeElement($identity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustments($type = null)
    {
        if (null === $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsRecursively($type = null)
    {
        $adjustments = $this->getAdjustments($type)->toArray();
        foreach ($this->items as $item) {
            $adjustments = array_merge($adjustments, $item->getAdjustmentsRecursively($type));
        }

        return $adjustments;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$this->hasAdjustment($adjustment)) {
            $this->adjustments->add($adjustment);
            $this->addToAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isLocked() && $this->hasAdjustment($adjustment)) {
            $this->adjustments->removeElement($adjustment);
            $this->subtractFromAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustment(AdjustmentInterface $adjustment)
    {
        return $this->adjustments->contains($adjustment);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotal($type = null)
    {
        if (null === $type) {
            return $this->adjustmentsTotal;
        }

        $total = 0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotalRecursively($type = null)
    {
        $total = 0;
        foreach ($this->getAdjustmentsRecursively($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustments($type)
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            if ($adjustment->isLocked()) {
                continue;
            }

            $this->removeAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustmentsRecursively($type = null)
    {
        $this->removeAdjustments($type);
        foreach ($this->items as $item) {
            $item->removeAdjustmentsRecursively($type);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateAdjustmentsTotal()
    {
        $this->adjustmentsTotal = 0;

        foreach ($this->adjustments as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $this->adjustmentsTotal += $adjustment->getAmount();
            }
        }

        $this->recalculateTotal();
    }

    /**
     * Calculate total.
     * Items total + Adjustments total.
     */
    protected function recalculateTotal()
    {
        $this->total = $this->itemsTotal + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal += $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal -= $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInformation()
    {
        return $this->additionalInformation;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalInformation($information)
    {
        $this->additionalInformation = $information;
    }
}
