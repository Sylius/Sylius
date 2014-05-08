<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Model;

/**
 * Payments model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
    protected $amount = 0;

    /**
     * State.
     *
     * @var string
     */
    protected $state = PaymentInterface::STATE_NEW;

    /**
     * Credit card as a source.
     *
     * @var CreditCardInterface
     */
    protected $creditCard;

    /**
     * Creation date.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var array
     */
    protected $details = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
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
    public function getMethod()
    {
      return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod(PaymentMethodInterface $method = null)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        if (null !== $this->creditCard) {
            return $this->creditCard;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
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
    public function setState($state)
    {
        $this->state = $state;

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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDetails(array $details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails()
    {
        return $this->details;
    }
}
