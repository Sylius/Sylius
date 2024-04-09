<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Promotion;

trait FormTrait
{
    public function setStartsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getElement('field_starts_at_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('field_starts_at_time')->setValue(date('H:i', $timestamp));
    }

    public function setEndsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getElement('field_ends_at_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('field_ends_at_time')->setValue(date('H:i', $timestamp));
    }

    public function setUsageLimit(int $limit): void
    {
        $this->getElement('field_usage_limit')->setValue($limit);
    }

    public function makeExclusive(): void
    {
        $this->getElement('field_exclusive')->check();
    }

    public function makeNotAppliesToDiscountedItem(): void
    {
        $this->getElement('field_applies_to_discounted')->uncheck();
    }

    public function makeCouponBased(): void
    {
        $this->getElement('field_coupon_based')->check();
    }

    public function checkChannel(string $name): void
    {
        $this->getElement('channels')->checkField($name);
    }

    /** @return array<string, string> */
    protected function getDefinedFormElements(): array
    {
        return [
            'channels' => '#sylius_promotion_channels',
            'field_applies_to_discounted' => '#sylius_promotion_appliesToDiscounted',
            'field_coupon_based' => '#sylius_promotion_couponBased',
            'field_ends_at_date' => '#sylius_promotion_endsAt_date',
            'field_ends_at_time' => '#sylius_promotion_endsAt_time',
            'field_exclusive' => '#sylius_promotion_exclusive',
            'field_label' => '[name="sylius_promotion[translations][%localeCode%][label]"]',
            'field_name' => '#sylius_promotion_name',
            'field_usage_limit' => '#sylius_promotion_usageLimit',
            'field_starts_at_date' => '#sylius_promotion_startsAt_date',
            'field_starts_at_time' => '#sylius_promotion_startsAt_time',
        ];
    }
}
