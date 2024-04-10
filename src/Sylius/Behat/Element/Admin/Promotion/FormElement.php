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

namespace Sylius\Behat\Element\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Service\TabsHelper;
use Webmozart\Assert\Assert;

final class FormElement extends Element implements FormElementInterface
{
    public function prioritizeIt(?int $priority): void
    {
        $this->getElement('priority')->setValue($priority);
    }

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

    public function setLabel(string $label, string $localeCode): void
    {
        $this->getElement('label', ['%locale_code%' => $localeCode])->setValue($label);
    }

    public function hasLabel(string $label, string $localeCode): bool
    {
        return $label === $this->getElement('label', ['%locale_code%' => $localeCode])->getValue();
    }

    public function addAction(?string $actionName): void
    {
        $this->getElement('add_action_button')->press();
        $this->waitForFormUpdate();

        if (null !== $actionName) {
            $this->selectActionOption('Type', $actionName);
            $this->waitForFormUpdate();
        }
    }

    public function fillActionOption(string $option, string $value): void
    {
        $this->getLastAction()->fillField($option, $value);
    }

    public function fillActionOptionForChannel(string $channelCode, string $option, string $value): void
    {
        $lastAction = $this->getChannelConfigurationOfLastAction($channelCode);
        $lastAction->fillField($option, $value);
    }

    public function selectActionOption(string $option, string $value, bool $multiple = false): void
    {
        $this->getLastAction()->find('named', ['select', $option])->selectOption($value, $multiple);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'actions' => '#sylius_promotion_actions',
            'add_action_button' => '#sylius_promotion_actions_add',
            'applies_to_discounted' => '#sylius_promotion_appliesToDiscounted',
            'channels' => '#sylius_promotion_channels',
            'coupon_based' => '#sylius_promotion_couponBased',
            'ends_at_date' => '#sylius_promotion_endsAt_date',
            'ends_at_time' => '#sylius_promotion_endsAt_time',
            'exclusive' => '#sylius_promotion_exclusive',
            'form' => '[data-live-name-value="SyliusAdmin.Promotion.Form"]',
            'label' => '[name="sylius_promotion[translations][%locale_code%][label]"]',
            'name' => '#sylius_promotion_name',
            'priority' => '#sylius_promotion_priority',
            'usage_limit' => '#sylius_promotion_usageLimit',
            'starts_at_date' => '#sylius_promotion_startsAt_date',
            'starts_at_time' => '#sylius_promotion_startsAt_time',
        ]);
    }

    private function getLastAction(): NodeElement
    {
        $items = $this->getElement('actions')->findAll('css', '[data-test-promotion-action]');
        Assert::notEmpty($items);

        return end($items);
    }

    private function getChannelConfigurationOfLastAction(string $channelCode): NodeElement
    {
        $lastAction = $this->getLastAction();

        TabsHelper::switchTab($this->getSession(), $lastAction, $channelCode);

        return $lastAction
            ->find('css', sprintf('[id^="sylius_promotion_actions_"][id$="_configuration_%s"]', $channelCode))
        ;
    }

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }
}
