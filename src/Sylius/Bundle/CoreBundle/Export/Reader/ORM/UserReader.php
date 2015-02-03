<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Export user reader.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class UserReader extends AbstractDoctrineReader
{
    private $userRepository;
    
    public function __construct(RepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function process($user)
    {
        $shippingAddress = $user->getShippingAddress();
        $billingAddress = $user->getBillingAddress();
        $createdAt = (string) $user->getCreatedAt()->format('Y-m-d H:m:s');
        
        return array(
            'id'                            => $user->getId(),
            'first_name'                    => $user->getFirstName(),
            'last_name'                     => $user->getLastName(),
            'username'                      => $user->getUsername(),
            'email'                         => $user->getEmail(),
            'shipping_address_company'      => $shippingAddress ? $shippingAddress->getCompany() : null,
            'shipping_address_country'      => $shippingAddress ? $shippingAddress->getCountry() : null,
            'shipping_address_province'     => $shippingAddress ? $shippingAddress->getProvince() : null,
            'shipping_address_city'         => $shippingAddress ? $shippingAddress->getCity() : null,
            'shipping_address_street'       => $shippingAddress ? $shippingAddress->getStreet() : null,
            'shipping_address_postcode'     => $shippingAddress ? $shippingAddress->getPostcode() : null,
            'shipping_address_phone_number' => $shippingAddress ? $shippingAddress->getPhoneNumber() : null,
            'billingAddress'                => $billingAddress ? $billingAddress->getCompany() : null,
            'billing_address_country'       => $billingAddress ? $billingAddress->getCountry() : null,
            'billing_address_province'      => $billingAddress ? $billingAddress->getProvince() : null,
            'billing_address_city'          => $billingAddress ? $billingAddress->getCity() : null,
            'billing_address_street'        => $billingAddress ? $billingAddress->getStreet() : null,
            'billing_address_postcode'      => $billingAddress ? $billingAddress->getPostcode() : null,
            'billing_address_phone_number'  => $billingAddress ? $billingAddress->getPhoneNumber() : null,
            'enabled'                       => $user->isEnabled(),
            'currency'                      => $user->getCurrency(),
            'created_at'                    => $createdAt
        );
    }
    
    public function getQuery()
    {
        $query = $this->userRepository->createQueryBuilder('u')
            ->getQuery();
        
        return $query;
    }
}