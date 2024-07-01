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
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\CountsChannelBasedErrors;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use CountsChannelBasedErrors;
    use NamesIt;

    public function checkChannelsState(string $channelName): bool
    {
        $field = $this->getDocument()->findField($channelName);

        return (bool) $field->getValue();
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
        return $this->hasElement('manage_coupons_button');
    }

    public function manageCoupons(): void
    {
        $this->getElement('manage_coupons_button')->click();
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
        return $this->countChannelErrors($this->getElement('actions', ['%name%' => $channelCode]), $channelCode);
    }

    public function getRuleValidationErrorsCount(string $channelCode): int
    {
        return $this->countChannelErrors($this->getElement('rules'), $channelCode);
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'action_field' => '[id^="sylius_admin_promotion_actions_"][id$="_configuration_%channelCode%_%field%"]',
            'actions' => '#sylius_admin_promotion_actions',
            'applies_to_discounted' => '#sylius_admin_promotion_appliesToDiscounted',
            'code' => '#sylius_admin_promotion_code',
            'ends_at' => '#sylius_admin_promotion_endsAt',
            'ends_at_date' => '#sylius_admin_promotion_endsAt_date',
            'ends_at_time' => '#sylius_admin_promotion_endsAt_time',
            'exclusive' => '#sylius_admin_promotion_exclusive',
            'coupon_based' => '#sylius_admin_promotion_couponBased',
            'manage_coupons_button' => '[data-test-manage-coupons]',
            'name' => '#sylius_admin_promotion_name',
            'order_percentage_action_field' => '[id^="sylius_admin_promotion_actions_"][id$="_configuration_percentage"]',
            'priority' => '#sylius_admin_promotion_priority',
            'rule_amount' => '[id^="sylius_admin_promotion_rules_"][id$="_configuration_%channelCode%_amount"]',
            'rules' => '#sylius_admin_promotion_rules',
            'usage_limit' => '#sylius_admin_promotion_usageLimit',
            'starts_at' => '#sylius_admin_promotion_startsAt',
            'starts_at_date' => '#sylius_admin_promotion_startsAt_date',
            'starts_at_time' => '#sylius_admin_promotion_startsAt_time',
        ]);
    }
}
