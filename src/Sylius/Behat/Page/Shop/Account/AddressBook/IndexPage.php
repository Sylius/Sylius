<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use Sylius\Behat\Page\SymfonyPage;
 
/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_account_address_book_index';
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleAddressOnList()
    {
        return 1 === count($this->getElement('addresses')->findAll('css', '.title'));
    }

    /**
     * {@inheritdoc}
     */
    public function hasAddressFullName($fullName)
    {
        return null !== $this->getElement('addresses')->find('css', sprintf('.title:contains("%s")', $fullName));
    }

    /**
     * {@inheritdoc}
     */
    public function hasNoExistingAddressesMessage()
    {
        return false !== strpos($this->getElement('no_addresses_message' )->getText(), 'no addresses to display');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'addresses' => '#addresses',
            'no_addresses_message' => '#addresses > .message',
        ]);
    }
}
