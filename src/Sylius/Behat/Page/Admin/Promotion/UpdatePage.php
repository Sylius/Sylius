<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Promotion;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt;
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->getDocument()->fillField('Priority', $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getElement('priority')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function checkChannelsState($channelName)
    {
        $field = $this->getDocument()->findField($channelName);

        return (bool) $field->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function fillUsageLimit($limit)
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    public function makeExclusive()
    {
        $this->getDocument()->checkField('Exclusive');
    }

    public function checkCouponBased()
    {
        $this->getDocument()->checkField('Coupon based');
    }

    public function checkChannel($name)
    {
        $this->getDocument()->checkField($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartsAt(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_startsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_startsAt_time', date('H:i', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function setEndsAt(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_endsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_endsAt_time', date('H:i', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function hasStartsAt(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        return $this->getElement('starts_at_date')->getValue() === date('Y-m-d', $timestamp)
            && $this->getElement('starts_at_time')->getValue() === date('H:i', $timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function hasEndsAt(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        return $this->getElement('ends_at_date')->getValue() === date('Y-m-d', $timestamp)
            && $this->getElement('ends_at_time')->getValue() === date('H:i', $timestamp);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return [
            'code' => '#sylius_promotion_code',
            'priority' => '#sylius_promotion_priority',
            'coupon_based' => '#sylius_promotion_couponBased',
            'ends_at' => '#sylius_promotion_endsAt',
            'ends_at_date' => '#sylius_promotion_endsAt_date',
            'ends_at_time' => '#sylius_promotion_endsAt_time',
            'exclusive' => '#sylius_promotion_exclusive',
            'name' => '#sylius_promotion_name',
            'starts_at' => '#sylius_promotion_startsAt',
            'starts_at_date' => '#sylius_promotion_startsAt_date',
            'starts_at_time' => '#sylius_promotion_startsAt_time',
            'usage_limit' => '#sylius_promotion_usageLimit',
        ];
    }
}
