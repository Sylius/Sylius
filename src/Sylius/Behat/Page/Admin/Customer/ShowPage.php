<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Customer;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPage;
use Webmozart\Assert\Assert;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function isRegistered()
    {
        $username = $this->getDocument()->find('css', '#username');

        return null !== $username;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAccount()
    {
        $deleteButton = $this->getElement('delete_account_button');
        $deleteButton->pressButton('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail()
    {
        return $this->getElement('customer_email')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerPhoneNumber()
    {
        return $this->getElement('customer_phone_number')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerName()
    {
        return $this->getElement('customer_name')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistrationDate()
    {
        return new \DateTime(str_replace('Customer since ', '', $this->getElement('registration_date')->getText()));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAddress()
    {
        return $this->getElement('default_address')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAccount()
    {
        return $this->hasElement('no_account');
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedToNewsletter()
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
    public function hasDefaultAddressProvinceName($provinceName)
    {
        $defaultAddressProvince = $this->getElement('default_address')->getText();

        return false !== stripos($defaultAddressProvince, $provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasVerifiedEmail()
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
    public function getGroupName()
    {
        $group = $this->getElement('group');

        Assert::notNull($group, 'There should be element group on page.');

        list($text, $groupName) = explode(':', $group->getText());

        return trim($groupName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasEmailVerificationInformation()
    {
        return null === $this->getDocument()->find('css', '#verified-email');
    }

    /**
     * {@inheritdoc}
     */
    public function hasImpersonateButton()
    {
        return $this->hasElement('impersonate_button');
    }

    /**
     * {@inheritdoc}
     */
    public function impersonate()
    {
        $this->getElement('impersonate_button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomerPlacedAnyOrders()
    {
        return null !== $this->getElement('statistics')->find('css', '.sylius-orders-overall-count');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrdersCountInChannel($channelName)
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
    public function getOrdersTotalInChannel($channelName)
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
    public function getAverageTotalInChannel($channelName)
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
    public function getSuccessFlashMessage()
    {
        return trim($this->getElement('flash_message')->getText());
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_customer_show';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
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
     * @param string $channelName
     *
     * @return NodeElement
     *
     * @throws \InvalidArgumentException
     */
    private function getStatisticsForChannel($channelName)
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
