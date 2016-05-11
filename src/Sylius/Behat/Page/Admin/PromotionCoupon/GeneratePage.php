<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class GeneratePage extends SymfonyPage implements GeneratePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkAmountValidation($message)
    {
        $foundedElement = $this->getValidatedField($this->getElement('amount'));
        if (null === $foundedElement) {
            throw new ElementNotFoundException($this->getSession(), 'Element', 'css', '#sylius_promotion_coupon_instruction_amount');
        }

        return $message === $foundedElement->find('css', '.pointing')->getText();
    }

    public function generate()
    {
        $this->getDocument()->pressButton('Generate');
    }

    /**
     * {@inheritdoc}
     */
    public function specifyAmount($amount)
    {
        $this->getDocument()->fillField('Amount', $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $date)
    {
        $timestamp = $date->getTimestamp();

        $this->getDocument()->fillField('Expires at', date('Y-m-d', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageLimit($limit)
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouteName()
    {
        return 'sylius_admin_promotion_coupon_generate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_promotion_coupon_generate_instruction_amount',
            'usage_limit' => '#sylius_promotion_coupon_generate_instruction_usageLimit',
            'expires_at' => '#sylius_promotion_coupon_generate_instruction_expiresAt',
        ]);
    }

    /**
     * @param NodeElement $element
     *
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    private function getValidatedField(NodeElement $element)
    {
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
