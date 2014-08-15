<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Entity;

/**
 * SearchLog entity
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchLog
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $searchString;

    /**
     * @var string
     */
    private $remoteAddress;

    /**
     * @var \DateTime
     */
    private $createdAt;


    /**
     * Get id.
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set searchString
     *
     * @param string $searchString
     * @return SearchLog
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;

        return $this;
    }

    /**
     * Get searchString.
     *
     * @return string 
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * @param $remoteAddress
     *
     * @return $this
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;

        return $this;
    }

    /**
     * Get remoteAddress
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SearchLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
