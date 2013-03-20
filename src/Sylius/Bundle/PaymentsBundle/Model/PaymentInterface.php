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
 * Single payment interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PaymentInterface
{
    /**
     * Get payments identifier.
     *
     * @return mixed
     */
  public function getId();

  /**
   * Get payment method associated with this payment.
   *
   * @return PaymentMethodInterface
   */
  public function getMethod();

  /**
   * Set payment method.
   *
   * @param PaymentMethodInterface $method
   */
  public function setMethod(PaymentMethodInterface $method);

  /**
   * Get payment currency.
   *
   * @return string
   */
  public function getCurrency();

  /**
   * Set currency.
   *
   * @param string
   */
  public function setCurrency($currency);

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
   * Return the balance.
   *
   * @return integer
   */
  public function getBalance();

  /**
   * Get all transactions for this payment.
   *
   * @return Collection
   */
  public function getTransactions();

  /**
   * Add transaction to payment.
   *
   * @param TransactionInterface
   */
  public function addTransaction(TransactionInterface $transaction);

  /**
   * Remove transaction from payment.
   *
   * @param TransactionInterface
   */
  public function removeTransaction(TransactionInterface $transaction);

  /**
   * Has transaction?
   *
   * @return Boolean
   */
  public function hasTransaction(TransactionInterface $transaction);

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
