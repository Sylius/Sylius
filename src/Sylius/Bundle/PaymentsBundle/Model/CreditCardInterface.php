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

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Payment method interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CreditCardInterface extends PaymentSourceInterface, TimestampableInterface
{
    /**
     * Supported CC brands.
     */
    const BRAND_VISA               = 'visa';
    const BRAND_MASTERCARD         = 'mastercard';
    const BRAND_DISCOVER           = 'discover';
    const BRAND_AMEX               = 'amex';
    const BRAND_DINERS_CLUB        = 'diners_club';
    const BRAND_JCB                = 'jcb';
    const BRAND_SWITCH             = 'switch';
    const BRAND_SOLO               = 'solo';
    const BRAND_DANKORT            = 'dankort';
    const BRAND_MAESTRO            = 'maestro';
    const BRAND_FORBRUGSFORENINGEN = 'forbrugsforeningen';
    const BRAND_LASER              = 'laser';

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
     * Get the type of credit card.
     * VISA, MasterCard...
     *
     * @return string
     */
    public function getType();

    /**
     * Set the type of cc.
     *
     * @param string $type
     */
    public function setType($type);

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
     * Get last 4 digits of number.
     *
     * @return string
     */
    public function getMaskedNumber();

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
}
