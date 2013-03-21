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
 * Payment method interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CreditCardInterface extends PaymentSourceInterface
{
    /**
     * Get payments method identifier.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get payment gateway token.
     *
     * @return string
     */
    public function getToken();

    /**
     * Set payment gateway token.
     *
     * @param string $token
     */
    public function setToken($token);

    /**
     * Get owner.
     *
     * @return CreditCardOwnerInterface
     */
    public function getOwner();

    /**
     * Set owner.
     *
     * @param CreditCardOwnerInterface
     */
    public function setOwner(CreditCardOwnerInterface $owner);

    /**
     * Get cardholder name.
     *
     * @return string
     */
    public function getCardholderName();

    /**
     * Set cardholder name.
     *
     * @param string $cardholderName
     */
    public function setCardholderName($cardholderName);

    /**
     * Get number.
     *
     * @return string
     */
    public function getNumber();

    /**
     * Set number.
     *
     * @param string $number
     */
    public function setNumber($number);

    /**
     * Get security code.
     *
     * @return string
     */
    public function getSecurityCode();

    /**
     * Set security code.
     *
     * @param string $securityCode
     */
    public function setSecurityCode($securityCode);

    /**
     * Get expiry month.
     *
     * @return integer
     */
    public function getExpiryMonth();

    /**
     * Set expiry month.
     *
     * @param integer
     */
    public function setExpiryMonth($expiryMonth);

    /**
     * Get expiry year.
     *
     * @return integer
     */
    public function getExpiryYear();

    /**
     * Set expiry year.
     *
     * @param integer $expiryYear
     */
    public function setExpiryYear($expiryYear);

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Get last update time.
     *
     * @return DateTime
     */
    public function getUpdatedAt();
}
