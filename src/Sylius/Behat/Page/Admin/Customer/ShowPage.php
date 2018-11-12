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

namespace Sylius\Behat\Page\Admin\Customer;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function isRegistered(): bool
    {
        $username = $this->getDocument()->find('css', '#username');

        return null !== $username;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAccount(): void
    {
        $deleteButton = $this->getElement('delete_account_button');
        $deleteButton->pressButton('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail(): string
    {
        return $this->getElement('customer_email')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerPhoneNumber(): string
    {
        return $this->getElement('customer_phone_number')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerName(): string
    {
        return $this->getElement('customer_name')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistrationDate(): \DateTimeInterface
    {
        return new \DateTime(str_replace('Customer since ', '', $this->getElement('registration_date')->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAddress(): string
    {
        return $this->getElement('default_address')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAccount(): bool
    {
        return $this->hasElement('no_account');
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedToNewsletter(): bool
    {
        $subscribedToNewsletter = $this->getElement('subscribed_to_newsletter');
        if ($subscribedToNewsletter->find('css', 'i.green')) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefaultAddressProvinceName(string $provinceName): bool
    {
        $defaultAddressProvince = $this->getElement('default_address')->getText();

        return false !== stripos($defaultAddressProvince, $provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasVerifiedEmail(): bool
    {
        $verifiedEmail = $this->getElement('verified_email');
        if ($verifiedEmail->find('css', 'i.green')) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupName(): string
    {
        $group = $this->getElement('group');

        Assert::notNull($group, 'There should be element group on page.');

        [$text, $groupName] = explode(':', $group->getText());

        return trim($groupName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasEmailVerificationInformation(): bool
    {
        return null === $this->getDocument()->find('css', '#verified-email');
    }

    /**
     * {@inheritdoc}
     */
    public function hasImpersonateButton(): bool
    {
        return $this->hasElement('impersonate_button');
    }

    /**
     * {@inheritdoc}
     */
    public function impersonate(): void
    {
        $this->getElement('impersonate_button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomerPlacedAnyOrders(): bool
    {
        return null !== $this->getElement('statistics')->find('css', '.sylius-orders-overall-count');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrdersCountInChannel(string $channelName): int
    {
        return (int) $this
            ->getStatisticsForChannel($channelName)
            ->find('css', '.sylius-orders-count')
            ->getText()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrdersTotalInChannel(string $channelName): string
    {
        return $this
            ->getStatisticsForChannel($channelName)
            ->find('css', '.sylius-orders-total')
            ->getText()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getAverageTotalInChannel(string $channelName): string
    {
        return $this
            ->getStatisticsForChannel($channelName)
            ->find('css', '.sylius-order-average-total')
            ->getText()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessFlashMessage(): string
    {
        return trim($this->getElement('flash_message')->getText());
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_admin_customer_show';
    }

    /**
     * {@inheritdoc}
     */
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

        $statisticsRibs = array_filter($statisticsRibs, function (NodeElement $statistic) use ($channelName) {
            return $channelName === trim($statistic->getText());
        });

        $actualStatisticsCount = count($statisticsRibs);
        Assert::same(
            1,
            $actualStatisticsCount,
            sprintf(
                'Expected a single statistic for channel "%s", but %d were found.',
                $channelName,
                $actualStatisticsCount
            )
        );

        $statisticsContents = $this->getElement('statistics')->findAll('css', '.row');
        $contentIndexes = array_keys($statisticsRibs);

        return $statisticsContents[reset($contentIndexes)];
    }
}
