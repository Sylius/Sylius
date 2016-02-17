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

use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CreditCard implements CreditCardInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $cardholderName;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $securityCode;

    /**
     * @var int
     */
    protected $expiryMonth;

    /**
     * @var int
     */
    protected $expiryYear;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * @return string
     */
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
    }
}
