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
interface SearchLogInterface
{
    /**
     * Set searchString
     *
     * @param string $searchString
     *
     * @return SearchLogInterface
     */
    public function setSearchString($searchString);

    /**
     * Get searchString.
     *
     * @return string
     */
    public function getSearchString();

    /**
     * @param string $remoteAddress
     *
     * @return SearchLogInterface
     */
    public function setRemoteAddress($remoteAddress);

    /**
     * Get remoteAddress
     *
     * @return string
     */
    public function getRemoteAddress();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SearchLogInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();
}
