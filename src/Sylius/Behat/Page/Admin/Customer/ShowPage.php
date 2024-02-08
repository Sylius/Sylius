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

namespace Sylius\Behat\Page\Admin\Customer;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function isRegistered(): bool
    {
        $username = $this->getDocument()->find('css', '#username');

        return null !== $username;
    }

    public function deleteAccount(): void
    {
        $deleteButton = $this->getElement('delete_account_button');
        $deleteButton->pressButton('Delete');
    }

    public function getCustomerEmail(): string
    {
        return $this->getElement('customer_email')->getText();
    }

    public function getCustomerPhoneNumber(): string
    {
        return $this->getElement('customer_phone_number')->getText();
    }

    public function getCustomerName(): string
    {
        return $this->getElement('customer_name')->getText();
    }

    public function getRegistrationDate(): \DateTimeInterface
    {
        return new \DateTime(str_replace('Customer since ', '', $this->getElement('registration_date')->getText()));
    }

    public function getDefaultAddress(): string
    {
        return $this->getElement('default_address')->getText();
    }

    public function hasAccount(): bool
    {
        return $this->hasElement('no_account');
    }

    public function isSubscribedToNewsletter(): bool
    {
        $subscribedToNewsletter = $this->getElement('subscribed_to_newsletter');
        if ($subscribedToNewsletter->find('css', 'i.green')) {
            return true;
        }

        return false;
    }

    public function hasDefaultAddressProvinceName(string $provinceName): bool
    {
        $defaultAddressProvince = $this->getElement('default_address')->getText();

        return false !== stripos($defaultAddressProvince, $provinceName);
    }

    public function hasVerifiedEmail(): bool
    {
        $verifiedEmail = $this->getElement('verified_email');
        if ($verifiedEmail->find('css', 'i.green')) {
            return true;
        }

        return false;
    }

    public function getGroupName(): string
    {
        $group = $this->getElement('group');

        Assert::notNull($group, 'There should be element group on page.');

        [$text, $groupName] = explode(':', $group->getText());

        return trim($groupName);
    }

    public function hasEmailVerificationInformation(): bool
    {
        return null === $this->getDocument()->find('css', '#verified-email');
    }

    public function hasImpersonateButton(): bool
    {
        return $this->hasElement('impersonate_button');
    }

    public function impersonate(): void
    {
        $this->getElement('impersonate_button')->click();
    }

    public function hasCustomerPlacedAnyOrders(): bool
    {
        return null !== $this->getElement('statistics')->find('css', '.sylius-orders-overall-count');
    }

    public function getOrdersCountInChannel(string $channelName): int
    {
        return (int) $this
            ->getStatisticsForChannel($channelName)
            ->find('css', '.sylius-orders-count')
            ->getText()
        ;
    }

    public function getOrdersTotalInChannel(string $channelName): string
    {
        return $this
            ->getStatisticsForChannel($channelName)
            ->find('css', '.sylius-orders-total')
            ->getText()
        ;
    }

    public function getAverageTotalInChannel(string $channelName): string
    {
        return $this
            ->getStatisticsForChannel($channelName)
            ->find('css', '.sylius-order-average-total')
            ->getText()
        ;
    }

    public function getSuccessFlashMessage(): string
    {
        return trim($this->getElement('flash_message')->getText());
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_customer_show';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer_email' => '#info .content.extra > a',
            'customer_name' => '#info .content > a',
            'customer_phone_number' => '#phone-number',
            'default_address' => '#default-address',
            'delete_account_button' => '#actions',
            'flash_message' => '.ui.icon.positive.message .content p',
            'group' => '.group',
            'impersonate_button' => '#impersonate',
            'no_account' => '#no-account',
            'statistics' => '#statistics',
            'registration_date' => '#info .content .date',
            'subscribed_to_newsletter' => '#subscribed-to-newsletter',
            'verified_email' => '#verified-email',
        ]);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function getStatisticsForChannel(string $channelName): NodeElement
    {
        $statisticsRibs = $this
            ->getElement('statistics')
            ->findAll('css', '.row > .column > .statistic > .sylius-channel-name')
        ;

        $statisticsRibs = array_filter($statisticsRibs, fn (NodeElement $statistic) => $channelName === trim($statistic->getText()));

        $actualStatisticsCount = count($statisticsRibs);
        Assert::same(
            1,
            $actualStatisticsCount,
            sprintf(
                'Expected a single statistic for channel "%s", but %d were found.',
                $channelName,
                $actualStatisticsCount,
            ),
        );

        $statisticsContents = $this->getElement('statistics')->findAll('css', '.row');
        $contentIndexes = array_keys($statisticsRibs);

        return $statisticsContents[reset($contentIndexes)];
    }
}
