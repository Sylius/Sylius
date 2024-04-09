<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Promotion;

trait FormTrait
{
    public function setStartsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getElement('starts_at_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('starts_at_time')->setValue(date('H:i', $timestamp));
    }

    public function setEndsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getElement('ends_at_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('ends_at_time')->setValue(date('H:i', $timestamp));
    }

    public function setUsageLimit(int $limit): void
    {
        $this->getElement('usage_limit')->setValue($limit);
    }

    public function makeExclusive(): void
    {
        $this->getElement('exclusive')->check();
    }

    public function makeNotAppliesToDiscountedItem(): void
    {
        $this->getElement('applies_to_discounted')->uncheck();
    }

    public function makeCouponBased(): void
    {
        $this->getElement('coupon_based')->check();
    }

    public function checkChannel(string $name): void
    {
        $this->getElement('channels')->checkField($name);
    }

    public function specifyLabel(string $label, string $localeCode): void
    {
        $this->getElement('label', ['%locale_code%' => $localeCode])->setValue($label);
    }

    public function hasLabel(string $label, string $localeCode): bool
    {
        return $label === $this->getElement('label', ['%locale_code%' => $localeCode])->getValue();
    }

    /** @return array<string, string> */
    protected function getDefinedFormElements(): array
    {
        return [
            'channels' => '#sylius_promotion_channels',
            'applies_to_discounted' => '#sylius_promotion_appliesToDiscounted',
            'coupon_based' => '#sylius_promotion_couponBased',
            'ends_at_date' => '#sylius_promotion_endsAt_date',
            'ends_at_time' => '#sylius_promotion_endsAt_time',
            'exclusive' => '#sylius_promotion_exclusive',
            'label' => '[name="sylius_promotion[translations][%locale_code%][label]"]',
            'name' => '#sylius_promotion_name',
            'usage_limit' => '#sylius_promotion_usageLimit',
            'starts_at_date' => '#sylius_promotion_startsAt_date',
            'starts_at_time' => '#sylius_promotion_startsAt_time',
        ];
    }
}
