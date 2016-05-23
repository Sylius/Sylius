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
 * @author Grzegorz Sadowski <grzegorz.sadowksi@lakion.com>
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
     * @param string $name
     *
     * @return bool
     */
    public function hasCustomerName($name)
    {
        return $this->hasValueInCustomerSection($name);
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function hasCustomerEmail($email)
    {
        return $this->hasValueInCustomerSection($email);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'customer' => '#customer-information',
        ]);
    }
    
    private function hasValueInCustomerSection($value)
    {
        $customerText = $this->getElement('customer')->getText();

        return stripos($customerText, $value) !== false;
    }
}
