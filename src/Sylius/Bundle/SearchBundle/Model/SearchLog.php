<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Model;

/**
 * SearchLog entity
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchLog implements SearchLogInterface
{
    /**
     * @var int
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchString($searchString)
    {
        $this->searchString = $searchString;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchString()
    {
        return $this->searchString;
    }

    /**
     * {@inheritdoc}
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
