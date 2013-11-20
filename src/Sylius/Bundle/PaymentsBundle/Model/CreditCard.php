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
 * Credit card model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CreditCard implements CreditCardInterface
{
    /**
     * Credit card identifier.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Special token for payment gateway.
     *
     * @var string
     */
    protected $token;

    /**
     * CC type.
     *
     * @var string
     */
    protected $type;

    /**
     * Cardholder name.
     *
     * @var string
     */
    protected $cardholderName;

    /**
     * Card number.
     *
     * @var string
     */
    protected $number;

    /**
     * Security code.
     *
     * @var string
     */
    protected $securityCode;

    /**
     * Expiry month number.
     *
     * @var integer
     */
    protected $expiryMonth;

    /**
     * Expiry year number.
     *
     * @var integer
     */
    protected $expiryYear;

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
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getMaskedNumber();
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
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCardholderName()
    {
        return $this->cardholderName;
    }

    /**
     * {@inheritdoc}
     */
    public function setCardholderName($cardholderName)
    {
        $this->cardholderName = $cardholderName;

        return $this;
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

        return $this;
    }

    public function getMaskedNumber()
    {
        return sprintf('XXXX XXXX XXXX %s', substr($this->number, -4));
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityCode()
    {
        return $this->securityCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecurityCode($securityCode)
    {
        $this->securityCode = $securityCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryMonth()
    {
        return $this->expiryMonth;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryMonth($expiryMonth)
    {
        $this->expiryMonth = $expiryMonth;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiryYear()
    {
        return $this->expiryYear;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiryYear($expiryYear)
    {
        $this->expiryYear = $expiryYear;

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
}
