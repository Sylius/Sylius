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
        return $this->checkValidationMessageFor('amount', $message);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCodeLengthValidation($message)
    {
        return $this->checkValidationMessageFor('code_length', $message);
    }

    /**
     * {@inheritdoc}
     */
    public function checkGenerationValidation($message)
    {
        return false !== strpos($this->getElement('form')->find('css', '.ui.red.label')->getText(), $message);
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
    public function specifyCodeLength($codeLength)
    {
        $this->getDocument()->fillField('Code length', $codeLength);
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
    public function getRouteName()
    {
        return 'sylius_admin_promotion_coupon_generate';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_promotion_coupon_generator_instruction_amount',
            'code_length' => '#sylius_promotion_coupon_generator_instruction_codeLength',
            'expires_at' => '#sylius_promotion_coupon_generator_instruction_expiresAt',
            'form' => '.two.column.stackable.grid',
            'usage_limit' => '#sylius_promotion_coupon_generator_instruction_usageLimit',
        ]);
    }

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    private function checkValidationMessageFor($element, $message)
    {
        $foundElement = $this->getElement($element);
        $validatedField = $this->getValidatedField($foundElement);
        if (null === $validatedField) {
            throw new ElementNotFoundException($this->getSession(), 'Element', 'css', $foundElement);
        }

        return $message === $validatedField->find('css', '.sylius-validation-error')->getText();
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
