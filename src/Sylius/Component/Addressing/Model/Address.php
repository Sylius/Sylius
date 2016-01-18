<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class Address implements AddressInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * @var string
     */
    protected $organization;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $administrativeArea;

    /**
     * @var string
     */
    protected $locality;

    /**
     * @var string
     */
    protected $dependentLocality;

    /**
     * @var string
     */
    protected $firstAddressLine;

    /**
     * @var string
     */
    protected $secondAddressLine;

    /**
     * @var string
     */
    protected $postcode;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountry($country = null)
    {
        if (null === $country) {
            $this->administrativeArea = null;
        }

        $this->country = $country;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdministrativeArea($administrativeArea = null)
    {
        if (null === $this->country) {
            return;
        }

        $this->administrativeArea = $administrativeArea;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependentLocality()
    {
        return $this->dependentLocality;
    }

    /**
     * {@inheritdoc}
     */
    public function setDependentLocality($dependentLocality)
    {
        $this->dependentLocality = $dependentLocality;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstAddressLine()
    {
        return $this->firstAddressLine;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstAddressLine($firstAddressLine)
    {
        $this->firstAddressLine = $firstAddressLine;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecondAddressLine()
    {
        return $this->secondAddressLine;
    }

    /**
     * {@inheritdoc}
     */
    public function setSecondAddressLine($secondAddressLine)
    {
        $this->secondAddressLine = $secondAddressLine;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * {@inheritdoc}
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
