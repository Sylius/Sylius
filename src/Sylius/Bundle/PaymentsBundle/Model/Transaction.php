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

/**
 * Payment transaction model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Transaction implements TransactionInterface
{
    /**
     * Payments method identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Payment.
     *
     * @var PaymentInterface
     */
    protected $payment;

    /**
     * Amount.
     *
     * @var string
     */
    protected $amount;

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
        $this->createdAt = new \DateTime('now');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPayment()
    {
      return $this->payment;
    }

    public function setPayment(PaymentInterface $payment = null)
    {
      $this->payment = $payment;

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

    public function getCurrency()
    {
      if (null === $this->payment) {
        throw new \BadMethodCallException('Cannot get transaction currency without payment assigned.');
      }

      return $this->payment->getCurrency();
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
