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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CreditCardInterface extends PaymentSourceInterface, TimestampableInterface, ResourceInterface
{
    /**
     * Supported CC brands.
     */
    const BRAND_VISA = 'visa';
    const BRAND_MASTERCARD = 'mastercard';
    const BRAND_DISCOVER = 'discover';
    const BRAND_AMEX = 'amex';
    const BRAND_DINERS_CLUB = 'diners_club';
    const BRAND_JCB = 'jcb';
    const BRAND_SWITCH = 'switch';
    const BRAND_SOLO = 'solo';
    const BRAND_DANKORT = 'dankort';
    const BRAND_MAESTRO = 'maestro';
    const BRAND_FORBRUGSFORENINGEN = 'forbrugsforeningen';
    const BRAND_LASER = 'laser';

    /**
     * @return string
     */
    public function getToken();

    /**
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
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getCardholderName();

    /**
     * @param string $cardholderName
     */
    public function setCardholderName($cardholderName);

    /**
     * @return string
     */
    public function getNumber();

    /**
     * @param string $number
     */
    public function setNumber($number);

    /**
     * @return string
     */
    public function getMaskedNumber();

    /**
     * @return string
     */
    public function getSecurityCode();

    /**
     * @param string $securityCode
     */
    public function setSecurityCode($securityCode);

    /**
     * @return int
     */
    public function getExpiryMonth();

    /**
     * @param int
     */
    public function setExpiryMonth($expiryMonth);

    /**
     * @return int
     */
    public function getExpiryYear();

    /**
     * @param int $expiryYear
     */
    public function setExpiryYear($expiryYear);
}
