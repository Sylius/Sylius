<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt;
    use ChecksCodeImmutability;

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

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return [
            'applies_to_discounted' => '#sylius_promotion_appliesToDiscounted',
            'code' => '#sylius_promotion_code',
            'coupon_based' => '#sylius_promotion_couponBased',
            'ends_at' => '#sylius_promotion_endsAt',
            'ends_at_date' => '#sylius_promotion_endsAt_date',
            'ends_at_time' => '#sylius_promotion_endsAt_time',
            'exclusive' => '#sylius_promotion_exclusive',
            'name' => '#sylius_promotion_name',
            'priority' => '#sylius_promotion_priority',
            'rules' => '#rules',
            'starts_at' => '#sylius_promotion_startsAt',
            'starts_at_date' => '#sylius_promotion_startsAt_date',
            'starts_at_time' => '#sylius_promotion_startsAt_time',
            'usage_limit' => '#sylius_promotion_usageLimit',
        ];
    }
}
