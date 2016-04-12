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

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Payment implements PaymentInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var PaymentMethodInterface
     */
    protected $method;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var int
     */
    protected $amount = 0;

    /**
     * @var string
     */
    protected $state = PaymentInterface::STATE_NEW;

    /**
     * @var CreditCardInterface
     */
    protected $creditCard;

    /**
     * @var array
     */
    protected $details = [];

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
        if (!is_int($amount)) {
            throw new \InvalidArgumentException('Amount must be an integer.');
        }

        $this->amount = $amount;
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
    }

    /**
     * {@inheritdoc}
     */
    public function setDetails($details)
    {
        if ($details instanceof \Traversable) {
            $details = iterator_to_array($details);
        }

        if (!is_array($details)) {
            throw new UnexpectedTypeException($details, 'array');
        }

        $this->details = $details;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails()
    {
        return $this->details;
    }
}
