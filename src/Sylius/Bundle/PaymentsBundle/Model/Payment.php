<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Payments model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Payment implements PaymentInterface
{
    /**
     * Payments method identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Method.
     *
     * @var PaymentMethodInterface
     */
    protected $method;

    /**
     * Currency.
     *
     * @var string
     */
    protected $currency;

    /**
     * Amount.
     *
     * @var integer
     */
    protected $amount;

    /**
     * Transactions.
     *
     * @var Collection
     */
    protected $transactions;

    /**
     * Credit card as a source.
     *
     * @var CreditCardInterface
     */
    protected $creditCard;

    /**
     * Creation date.
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->amount = 0;
        $this->transactions = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function getMethod()
    {
      return $this->method;
    }

    public function setMethod(PaymentMethodInterface $method)
    {
        $this->method = $method;

        return $this;
    }

    public function setSource(PaymentSourceInterface $source = null)
    {
        if (null === $source) {
            $this->creditCard = null;
        }

        if ($source instanceof CreditCardInterface) {
            $this->creditCard = $source;
        }

        return $this;
    }

    public function getSource()
    {
        if (null !== $this->creditCard) {
            return $this->creditCard;
        }
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    public function getAmount()
    {
      return $this->amount;
    }

    public function setAmount($amount)
    {
      $this->amount = $amount;

      return $this;
    }

    public function addTransaction(TransactionInterface $transaction)
    {
      if (!$this->hasTransaction($transaction)) {
        $transaction->setPayment($this);
        $this->transactions->add($transaction);
      }

      return $this;
    }

    public function getTransactions()
    {
        return $this->transactions;
    }

    public function hasTransaction(TransactionInterface $transaction)
    {
        return $this->transactions->contains($transaction);
    }

    public function getBalance()
    {
        $total = 0;

        foreach ($this->transactions as $transaction) {
            $total += $transaction->getAmount();
        }

        return $this->amount - $total;
    }

    public function removeTransaction(TransactionInterface $transaction)
    {
        if ($this->hasTransaction($transaction)) {
            $transaction->setPayment(null);
            $this->transactions->removeElement($transaction);
        }
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
