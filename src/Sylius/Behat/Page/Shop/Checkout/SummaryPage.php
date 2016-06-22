<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class SummaryPage extends SymfonyPage implements SummaryPageInterface
{
    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param TableAccessorInterface $tableAccessor
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
    }


    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemWithProductAndQuantity($productName, $quantity)
    {
        $table = $this->getElement('items_table');

        try {
            $this->tableAccessor->getRowWithFields($table, ['item' => $productName, 'qty' => $quantity]);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingAddress(AddressInterface $address)
    {
        $shippingAddress = $this->getElement('shipping_address')->getText();

        return $this->isAddressValid($shippingAddress, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingAddress(AddressInterface $address)
    {
        $billingAddress = $this->getElement('billing_address')->getText();

        return $this->isAddressValid($billingAddress, $address);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address' => '#addresses div:contains("Billing address") address',
            'shipping_address' => '#addresses div:contains("Shipping address") address',
            'items_table' => '#items table',
        ]);
    }

    /**
     * @param string $displayedAddress
     * @param AddressInterface $address
     *
     * @return bool
     */
    private function isAddressValid($displayedAddress, AddressInterface $address)
    {
        return
            $this->hasAddressPart($displayedAddress, $address->getCompany(), true) &&
            $this->hasAddressPart($displayedAddress, $address->getFirstName()) &&
            $this->hasAddressPart($displayedAddress, $address->getLastName()) &&
            $this->hasAddressPart($displayedAddress, $address->getPhoneNumber(), true) &&
            $this->hasAddressPart($displayedAddress, $address->getStreet()) &&
            $this->hasAddressPart($displayedAddress, $address->getCity()) &&
            $this->hasAddressPart($displayedAddress, $address->getProvinceCode(), true) &&
            $this->hasAddressPart($displayedAddress, $this->getCountryName($address->getCountryCode())) &&
            $this->hasAddressPart($displayedAddress, $address->getPostcode())
        ;
    }

    /**
     * @param string $address
     * @param string $addressPart
     *
     * @return bool
     */
    private function hasAddressPart($address, $addressPart, $optional = false)
    {
        if ($optional && null === $addressPart) {
            return true;
        }

        return false !== strpos($address, $addressPart);
    }

    /**
     * @param string $countryCode
     *
     * @return string
     */
    private function getCountryName($countryCode)
    {
        return strtoupper(Intl::getRegionBundle()->getCountryName($countryCode, 'en'));
    }
}
