<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Import\Writer\ORM;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * User writer.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class UserWriter extends AbstractDoctrineWriter
{
    private $userRepository;
    
    public function __construct(RepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function process($data) 
    {
        $user = $this->userRepository->createNew();
        $shippingAddress = $user->setShippingAddress();
        $billingAddress = $user->setBillingAddress();
        
        $user->setId();
        $user->setFirstName();
        $user->setLastName();
        $user->setUsername();
        $user->setEmail();
        $shippingAddress ? $shippingAddress->setCompany() : null;
        $shippingAddress ? $shippingAddress->setCountry() : null;
        $shippingAddress ? $shippingAddress->setProvince() : null;
        $shippingAddress ? $shippingAddress->setCity() : null;
        $shippingAddress ? $shippingAddress->setStreet() : null;
        $shippingAddress ? $shippingAddress->setPostcode() : null;
        $shippingAddress ? $shippingAddress->setPhoneNumber() : null;
        $billingAddress ? $billingAddress->setCompany() : null;
        $billingAddress ? $billingAddress->setCountry() : null;
        $billingAddress ? $billingAddress->setProvince() : null;
        $billingAddress ? $billingAddress->setCity() : null;
        $billingAddress ? $billingAddress->setStreet() : null;
        $billingAddress ? $billingAddress->setPostcode() : null;
        $billingAddress ? $billingAddress->setPhoneNumber() : null;
        $user->isEnabled();
        $user->setCurrency();
        $user->setCreatedAt();
    }
    
    public function getQuery()
    {
        $query = $this->userRepository->createQueryBuilder('u')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_user';
    }
}