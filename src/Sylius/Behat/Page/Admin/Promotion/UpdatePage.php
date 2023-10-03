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

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\CountsChannelBasedErrors;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use CountsChannelBasedErrors;
    use NamesIt;

    public function setPriority(?int $priority): void
    {
        $this->getDocument()->fillField('Priority', $priority);
    }

    public function getPriority(): int
    {
        return (int) $this->getElement('priority')->getValue();
    }

    public function checkChannelsState(string $channelName): bool
    {
        $field = $this->getDocument()->findField($channelName);

        return (bool) $field->getValue();
    }

    public function fillUsageLimit(string $limit): void
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    public function makeExclusive(): void
    {
        $this->getDocument()->checkField('Exclusive');
    }

    public function checkCouponBased(): void
    {
        $this->getDocument()->checkField('Coupon based');
    }

    public function checkChannel(string $name): void
    {
        $this->getDocument()->checkField($name);
    }

    public function setStartsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_startsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_startsAt_time', date('H:i', $timestamp));
    }

    public function setEndsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_endsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_endsAt_time', date('H:i', $timestamp));
    }

    public function hasStartsAt(\DateTimeInterface $dateTime): bool
    {
        $timestamp = $dateTime->getTimestamp();

        return $this->getElement('starts_at_date')->getValue() === date('Y-m-d', $timestamp) &&
            $this->getElement('starts_at_time')->getValue() === date('H:i', $timestamp);
    }

    public function hasEndsAt(\DateTimeInterface $dateTime): bool
    {
        $timestamp = $dateTime->getTimestamp();

        return $this->getElement('ends_at_date')->getValue() === date('Y-m-d', $timestamp) &&
            $this->getElement('ends_at_time')->getValue() === date('H:i', $timestamp);
    }

    public function isCouponManagementAvailable(): bool
    {
        $element = $this->getDocument()->find('css', 'a:contains("Manage coupons")');

        return null !== $element;
    }

    public function manageCoupons(): void
    {
        $this->getDocument()->clickLink('Manage coupons');
    }

    public function hasAnyRule(): bool
    {
        $items = $this->getElement('rules')->findAll('css', 'div[data-form-collection="item"]');

        return 0 < count($items);
    }

    public function hasRule(string $name): bool
    {
        $items = $this->getElement('rules')->findAll('css', 'div[data-form-collection="item"]');

        foreach ($items as $item) {
            $selectedOption = $item->find('css', 'option[selected="selected"]');

            /** @var NodeElement $selectedOption */
            if ($selectedOption->getText() === $name) {
                return true;
            }
        }

        return false;
    }

    public function removeActionFieldValue(string $channelCode, string $field): void
    {
        $this->getElement('action_field', ['%channelCode%' => $channelCode, '%field%' => $field])->setValue('');
    }

    public function getItemPercentageDiscountActionValue(string $channelCode): string
    {
        return $this->getElement('action_field', ['%channelCode%' => $channelCode, '%field%' => 'percentage'])->getValue() . '%';
    }

    public function specifyOrderPercentageDiscountActionValue(string $discount): void
    {
        $this->getElement('order_percentage_action_field')->setValue($discount);
    }

    public function getOrderPercentageDiscountActionValue(): string
    {
        $action = $this->getElement('order_percentage_action_field');

        return $action->find('css', 'input')->getValue() . '%';
    }

    public function removeRuleAmount(string $channelCode): void
    {
        $this->getElement('rule_amount', ['%channelCode%' => $channelCode])->setValue('');
    }

    public function getActionValidationErrorsCount(string $channelCode): int
    {
        return $this->countChannelErrors($this->getElement('actions'), $channelCode);
    }

    public function getRuleValidationErrorsCount(string $channelCode): int
    {
        return $this->countChannelErrors($this->getElement('rules'), $channelCode);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForTranslation(string $element, string $localeCode): string
    {
        $foundElement = $this->getElement($element, ['%localeCode%' => $localeCode])->getParent();

        $validationMessage = $foundElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return [
            'action_field' => '[id^="sylius_promotion_actions_"][id$="_configuration_%channelCode%_%field%"]',
            'actions' => '#actions',
            'applies_to_discounted' => '#sylius_promotion_appliesToDiscounted',
            'code' => '#sylius_promotion_code',
            'coupon_based' => '#sylius_promotion_couponBased',
            'ends_at' => '#sylius_promotion_endsAt',
            'ends_at_date' => '#sylius_promotion_endsAt_date',
            'ends_at_time' => '#sylius_promotion_endsAt_time',
            'exclusive' => '#sylius_promotion_exclusive',
            'label' => '#sylius_promotion_translations_%localeCode%_label',
            'name' => '#sylius_promotion_name',
            'order_percentage_action_field' => '[id^="sylius_promotion_actions_"][id$="_configuration_percentage"]',
            'priority' => '#sylius_promotion_priority',
            'rule_amount' => '[id^="sylius_promotion_rules_"][id$="_configuration_%channelCode%_amount"]',
            'rules' => '#rules',
            'starts_at' => '#sylius_promotion_startsAt',
            'starts_at_date' => '#sylius_promotion_startsAt_date',
            'starts_at_time' => '#sylius_promotion_startsAt_time',
            'usage_limit' => '#sylius_promotion_usageLimit',
        ];
    }
}
