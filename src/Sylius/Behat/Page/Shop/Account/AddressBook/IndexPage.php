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

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPage;
use Webmozart\Assert\Assert;

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
    public function getAddressesCount()
    {
        $addressesCount = count($this->getElement('addresses')->findAll('css', 'address'));

        if (!$this->hasNoDefaultAddress()) {
            ++$addressesCount;
        }

        return $addressesCount;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAddressOf($fullName)
    {
        return null !== $this->getAddressOf($fullName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasNoAddresses()
    {
        return $this->getDocument()->hasContent('You have no addresses defined');
    }

    /**
     * {@inheritdoc}
     */
    public function addressOfContains($fullName, $value)
    {
        $address = $this->getAddressOf($fullName);

        return $address->has('css', sprintf('address:contains("%s")', $value));
    }

    /**
     * {@inheritdoc}
     */
    public function editAddress($fullName)
    {
        $addressToEdit = $this->getAddressOf($fullName);
        $addressToEdit->findLink('Edit')->press();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAddress($fullName)
    {
        $addressToDelete = $this->getAddressOf($fullName);
        $addressToDelete->pressButton('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function setAsDefault($fullName)
    {
        $addressToSetAsDefault = $this->getAddressOf($fullName);
        $addressToSetAsDefault->pressButton('Set as default');
    }

    /**
     * {@inheritdoc}
     */
    public function hasNoDefaultAddress()
    {
        return !$this->hasElement('default_address');
    }

    /**
     * {@inheritdoc}
     */
    public function getFullNameOfDefaultAddress()
    {
        $fullNameElement = $this->getElement('default_address')->find('css', 'address > strong');

        Assert::notNull($fullNameElement, 'There should be a default address\'s full name.');

        return $fullNameElement->getText();
    }

    /**
     * @param string $fullName
     *
     * @return NodeElement|null
     */
    private function getAddressOf($fullName)
    {
        return $this->getElement('addresses')->find('css', sprintf('div.address:contains("%s")', $fullName));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'addresses' => '#sylius-addresses',
            'default_address' => '#sylius-default-address',
        ]);
    }
}
