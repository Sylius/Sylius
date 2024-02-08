<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class GeneratePage extends SymfonyPage implements GeneratePageInterface
{
    public function checkAmountValidation(string $message): bool
    {
        return $this->checkValidationMessageFor('amount', $message);
    }

    public function checkCodeLengthValidation(string $message): bool
    {
        return $this->checkValidationMessageFor('code_length', $message);
    }

    public function checkGenerationValidation(string $message): bool
    {
        return str_contains($this->getElement('form')->find('css', '.ui.red.label')->getText(), $message);
    }

    public function generate(): void
    {
        $this->getDocument()->pressButton('Generate');
    }

    public function specifyAmount(?int $amount): void
    {
        $this->getDocument()->fillField('Amount', $amount);
    }

    public function specifyCodeLength(?int $codeLength): void
    {
        $this->getDocument()->fillField('Code length', $codeLength);
    }

    public function setExpiresAt(\DateTimeInterface $date): void
    {
        $timestamp = $date->getTimestamp();

        $this->getDocument()->fillField('Expires at', date('Y-m-d', $timestamp));
    }

    public function setUsageLimit(int $limit): void
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    public function specifyPrefix(string $prefix): void
    {
        $this->getDocument()->fillField('Prefix', $prefix);
    }

    public function specifySuffix(string $suffix): void
    {
        $this->getDocument()->fillField('Suffix', $suffix);
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_promotion_coupon_generate';
    }

    protected function getDefinedElements(): array
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
     * @throws ElementNotFoundException
     */
    private function checkValidationMessageFor(string $element, string $message): bool
    {
        $foundElement = $this->getElement($element);
        $validatedField = $this->getValidatedField($foundElement);

        return $message === $validatedField->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getValidatedField(NodeElement $element): NodeElement
    {
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
