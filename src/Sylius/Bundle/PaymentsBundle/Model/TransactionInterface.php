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
 * Transaction interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TransactionInterface
{
    /**
     * Get payments transaction identifier.
     *
     * @return mixed
     */
  public function getId();

  /**
   * Get payment associated with this transaction.
   *
   * @return PaymentInterface
   */
  public function getPayment();

  /**
   * Set payment.
   *
   * @param null|PaymentInterface $payment
   */
  public function setPayment(PaymentInterface $payment = null);

  /**
   * Get trasnaction currency via the payment.
   *
   * @return string
   */
  public function getCurrency();

  /**
    * Get amount.
    *
    * @return integer
    */
  public function getAmount();

  /**
   * Set amount.
   *
   * @param integer $amount
   */
  public function setAmount($amount);

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
