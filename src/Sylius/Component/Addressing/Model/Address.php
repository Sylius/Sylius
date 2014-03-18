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

use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Default address model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class Address implements AddressInterface
{
    /**
     * Address id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * First name.
     *
     * @var string
     */
    protected $firstName;

    /**
     * Last name.
     *
     * @var string
     */
    protected $lastName;

    /**
     * Company.
     *
     * @var string
     */
    protected $company;

    /**
     * Country.
     *
     * @var CountryInterface
     */
    protected $country;

    /**
     * Province.
     *
     * @var ProvinceInterface
     */
    protected $province;

    /**
     * Street.
     *
     * @var string
     */
    protected $street;

    /**
     * City.
     *
     * @var string
     */
    protected $city;

    /**
     * Postcode.
     *
     * @var string
     */
    protected $postcode;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Update time.
     *
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

        return $this;
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

        return $this;
    }

    public function getFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * {@inheritdoc}
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
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
    public function setCountry(CountryInterface $country = null)
    {
        if (null === $country) {
            $this->province = null;
        }

        $this->country = $country;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvince(ProvinceInterface $province = null)
    {
        if (null === $this->country) {
            throw new \BadMethodCallException('Cannot define province on address without assigned country');
        }

        if (null !== $province && !$this->country->hasProvince($province)) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot set province "%s", because it does not belong to country "%s"',
                $province->getName(),
                $this->country->getName()
            ));
        }

        $this->province = $province;

        return $this;
    }

    public function isValidProvince(ExecutionContextInterface $context)
    {
        if (!$this->isValid()) {
            $context->addViolationAt('province', 'sylius.address.province.valid', array(), null);
        }
    }

    public function isValid()
    {
        if (null === $this->country) {
            return false;
        }

        if (!$this->country->hasProvinces()) {
            return true;
        }

        if (null === $this->province) {
            return false;
        }

        if ($this->country->hasProvince($this->province)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * {@inheritdoc}
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * {@inheritdoc}
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
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

        return $this;
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
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
