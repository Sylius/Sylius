<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Model;

/**
 * Abstract model class for address.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
abstract class Address implements AddressInterface
{
    /**
     * Address id.
     * 
     * @var integer
     */
    protected $id;
    
    /**
     * Company name.
     * 
     * @var string
     */
    protected $company;
    
    /**
     * Name.
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Surname.
     * 
     * @var string
     */
    protected $surname;
    
    /**
     * Street.
     * 
     * @var string
     */
    protected $street;
    
    /**
     * Postal code.
     * 
     * @var string
     */
    protected $postcode;
    
    /**
     * City name.
     * 
     * @var string
     */
    protected $city;

    /**
     * Creation time.
     * 
     * @var \DateTime
     */
    protected $createdAt;
    
    /**
     * Modification time.
     * 
     * @var \DateTime
     */
    protected $updatedAt;
    
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
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSurname()
    {
        return $this->surname;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
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
    public function incrementCreatedAt()
    {
        $this->createdAt = new \DateTime();
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
    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }
}
