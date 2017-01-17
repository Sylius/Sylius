<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class DashboardPage extends SymfonyPage implements DashboardPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_account_dashboard';
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomerName($name)
    {
        return $this->hasValueInCustomerSection($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomerEmail($email)
    {
        return $this->hasValueInCustomerSection($email);
    }

    /**
     * {@inheritdoc}
     */
    public function isVerified()
    {
        return !$this->hasElement('verification');
    }

    /**
     * {@inheritdoc}
     */
    public function hasResendVerificationEmailButton()
    {
        return $this->getDocument()->hasButton('Verify');
    }

    /**
     * {@inheritdoc}
     */
    public function pressResendVerificationEmail()
    {
        $this->getDocument()->pressButton('Verify');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'customer' => '#customer-information',
            'verification' => '#verification-form',
        ]);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function hasValueInCustomerSection($value)
    {
        $customerText = $this->getElement('customer')->getText();

        return stripos($customerText, $value) !== false;
    }
}
