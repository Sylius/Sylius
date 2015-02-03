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
 * Product writer.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductWriter extends AbstractDoctrineWriter
{
    private $productRepository;
    
    public function __construct(RepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    public function process($data) 
    {
        // $product = $this->productRepository->createNew();

        var_dump($data);

        // $shippingAddress = $user->setShippingAddress();
        // $billingAddress = $user->setBillingAddress();
        
        // $user->setFirstName($data['first_name']);
        // $user->setLastName($data['last_name']);
        // $user->setUsername($data['username']);
        // $user->setEmail($data['email']);
        // $shippingAddress ? $shippingAddress->setCompany($data['shipping_address_company']) : null;
        // $shippingAddress ? $shippingAddress->setCountry($data['shipping_address_country']) : null;
        // $shippingAddress ? $shippingAddress->setProvince($data['shipping_adress_province']) : null;
        // $shippingAddress ? $shippingAddress->setCity($data['shipping_address_city']) : null;
        // $shippingAddress ? $shippingAddress->setStreet($data['shipping_address_street']) : null;
        // $shippingAddress ? $shippingAddress->setPostcode($data['shipping_address_postcode']) : null;
        // $shippingAddress ? $shippingAddress->setPhoneNumber($data['shipping_adress_phone_number']) : null;
        // $billingAddress ? $billingAddress->setCompany($data['billing_address_company']) : null;
        // $billingAddress ? $billingAddress->setCountry($data['billing_address_country']) : null;
        // $billingAddress ? $billingAddress->setProvince($data['billing_adress_province']) : null;
        // $billingAddress ? $billingAddress->setCity($data['billing_address_city']) : null;
        // $billingAddress ? $billingAddress->setStreet($data['billing_address_street']) : null;
        // $billingAddress ? $billingAddress->setPostcode($data['billing_address_postcode']) : null;
        // $billingAddress ? $billingAddress->setPhoneNumber($data['billing_adress_phone_number']) : null;
        // $user->setEnabled($data['enabled']);
        // $user->setCurrency($data['currency']);
        // $user->setCreatedAt(new \DateTime($data['created_at']));


        // $archetype = $product->getArchetype();
        // $taxCategory = $product->getTaxCategory();
        // $shippingCategory = $product->getShippingCategory();
        // $createdAt = (string) $product->getCreatedAt()->format('Y-m-d H:m:s');
        
        // return array(
        //     'id' => $product->getId(),
        //     'name' => $product->getName(),
        //     'price' => $product->getPrice(),
        //     'description' => $product->getDescription(),
        //     'short_description' => $product->getShortDescription(),
        //     'archetype' => $archetype ? $archetype->getCode() : null,
        //     'tax_category' => $taxCategory ? $taxCategory->getName() : null,
        //     'shipping_category' => $shippingCategory ? $shippingCategory->getName() :null,
        //     'is_available_on' => $product->isAvailable(),
        //     'meta_keywords' => $product->getMetaKeywords(),
        //     'meta_description' => $product->getMetaDescription(),
        //     'createdAt' => $createdAt,
        // );
    }
    
    public function getQuery()
    {
        $query = $this->productRepository->createQueryBuilder('u')
            ->getQuery();
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'product';
    }
}