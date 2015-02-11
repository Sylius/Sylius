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
    private $addressRepository;
    private $countryRepository;
    private $provinceRepository;

    public function __construct(RepositoryInterface $userRepository, EntityManager $em, RepositoryInterface $addressRepository, RepositoryInterface $countryRepository, RepositoryInterface $provinceRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function process($data)
    {
        $userRepository = $this->userRepository;

        if ($userRepository->findOneBy(array('email' => $data['email']))) {
            $user = $userRepository->findOneByEmail($data['email']);
            $shippingAddress = $user->getShippingAddress();
            $billingAddress = $user->getBillingAddress();

            $data['shipping_address_country'] ? $shippingCountry = $this->countryRepository->findOneByIsoName($data['shipping_address_country']) : $shippingCountry = $shippingAddress->getCountry();
            $data['shipping_address_province'] ? $shippingProvince = $this->provinceRepository->findOneByIsoName($data['shipping_address_province']) : $shippingProvince = $shippingAddress->getProvince();
            $data['billing_address_country'] ? $billingCountry = $this->countryRepository->findOneByIsoName($data['billing_address_country']) : $billingCountry = $billingAddress->getCountry();
            $data['billing_address_province'] ? $billingProvince = $this->provinceRepository->findOneByIsoName($data['billing_address_province']) : $billingProvince = $billingAddress->getProvince();

            $data['first_name'] ? $user->setFirstName($data['first_name']) : $user->getFirstName();
            $data['last_name'] ? $user->setLastName($data['last_name']) : $user->getLastName();
            $data['email'] ? $user->setEmail($data['email']) : $user->getEmail();
            $data['shipping_address_company'] ? $shippingAddress->setCompany($data['shipping_address_company']) : $shippingAddress->getCompany();
            $data['first_name'] ? $shippingAddress->setFirstName($data['first_name']) : $shippingAddress->getFirstName();
            $data['last_name'] ? $shippingAddress->setLastName($data['last_name']) : $shippingAddress->getLastName();
            $shippingAddress->setCountry($shippingCountry);
            $shippingAddress->setProvince($shippingProvince);
            $data['shipping_address_city'] ? $shippingAddress->setCity($data['shipping_address_city']) : $shippingAddress->getCity();
            $data['shipping_address_street'] ? $shippingAddress->setStreet($data['shipping_address_street']) : $shippingAddress->getStreet();
            $data['shipping_address_postcode'] ? $shippingAddress->setPostcode($data['shipping_address_postcode']) : $shippingAddress->getPostcode();
            $data['shipping_address_phone_number'] ? $shippingAddress->setPhoneNumber($data['shipping_address_phone_number']) : $shippingAddress->getPhoneNumber();
            $user->setShippingAddress($shippingAddress);
            $data['billing_address_company'] ? $billingAddress->setCompany($data['billing_address_company']) : $billingAddress->getCompany();
            $data['first_name'] ? $billingAddress->setFirstName($data['first_name']) : $billingAddress->getFirstName();
            $data['last_name'] ? $billingAddress->setLastName($data['last_name']) : $billingAddress->getLastName();
            $billingAddress->setCountry($billingCountry);
            $billingAddress->setProvince($billingProvince);
            $data['billing_address_city'] ? $billingAddress->setCity($data['billing_address_city']) : $billingAddress->getCity();
            $data['billing_address_street'] ? $billingAddress->setStreet($data['billing_address_street']) : $billingAddress->getStreet();
            $data['billing_address_postcode'] ? $billingAddress->setPostcode($data['billing_address_postcode']) : $billingAddress->getPostcode();
            $data['billing_address_phone_number'] ? $billingAddress->setPhoneNumber($data['billing_address_phone_number']) : $billingAddress->getPhoneNumber();
            $user->setBillingAddress($billingAddress);
            $user->setEnabled($data['enabled']);
            $data['currency'] ? $user->setCurrency($data['currency']) : $user->getCurrency();
            $user->setPlainPassword($data['password']);
            $user->setUpdatedAt(new \DateTime());

            return $user;
        }

        $user = $userRepository->createNew();
        $shippingAddress = $this->addressRepository->createNew();
        $billingAddress = $this->addressRepository->createNew();
        $data['shipping_address_country'] ? $shippingCountry = $this->countryRepository->findOneByIsoName($data['shipping_address_country']) : $shippingCountry = null;
        $data['shipping_address_province'] ? $shippingProvince = $this->provinceRepository->findOneByIsoName($data['shipping_address_province']) : $shippingProvince = null;
        $data['billing_address_country'] ? $billingCountry = $this->countryRepository->findOneByIsoName($data['billing_address_country']) : $billingCountry = null;
        $data['billing_address_province'] ? $billingProvince = $this->provinceRepository->findOneByIsoName($data['billing_address_province']) : $billingProvince = null;

        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);
        $user->setEmail($data['email']);
        $data['shipping_address_company'] ? $shippingAddress->setCompany($data['shipping_address_company']) : null;
        $shippingAddress->setFirstName($data['first_name']);
        $shippingAddress->setLastName($data['last_name']);
        $shippingAddress->setCountry($shippingCountry);
        $shippingAddress->setProvince($shippingProvince);
        $data['shipping_address_city'] ? $shippingAddress->setCity($data['shipping_address_city']) : null;
        $data['shipping_address_street'] ? $shippingAddress->setStreet($data['shipping_address_street']) : null;
        $data['shipping_address_postcode'] ? $shippingAddress->setPostcode($data['shipping_address_postcode']) : null;
        $data['shipping_address_phone_number'] ? $shippingAddress->setPhoneNumber($data['shipping_address_phone_number']) : null;
        $user->setShippingAddress($shippingAddress);
        $data['billing_address_company'] ? $billingAddress->setCompany($data['billing_address_company']) : null;
        $billingAddress->setFirstName($data['first_name']);
        $billingAddress->setLastName($data['last_name']);
        $billingAddress->setCountry($billingCountry);
        $billingAddress->setProvince($billingProvince);
        $data['billing_address_city'] ? $billingAddress->setCity($data['billing_address_city']) : null;
        $data['billing_address_street'] ? $billingAddress->setStreet($data['billing_address_street']) : null;
        $data['billing_address_postcode'] ? $billingAddress->setPostcode($data['billing_address_postcode']) : null;
        $data['billing_address_phone_number'] ? $billingAddress->setPhoneNumber($data['billing_address_phone_number']) : null;
        $user->setBillingAddress($billingAddress);
        $user->setEnabled($data['enabled']);
        $user->setCurrency($data['currency']);
        $user->setPlainPassword($data['password']);
        $user->setCreatedAt(new \DateTime($data['created_at']));

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'import_user';
    }
}
