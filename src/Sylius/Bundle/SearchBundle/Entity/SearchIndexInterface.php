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
 * SearchIndex interface
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
interface SearchIndexInterface {

    /**
     * Set itemId
     *
     * @param integer $itemId
     * @return SyliusSearchIndex
     */
    public function setItemId($itemId);

    /**
     * Get itemId
     *
     * @return integer
     */
    public function getItemId();

    /**
     * Set entity
     *
     * @param string $entity
     * @return SyliusSearchIndex
     */
    public function setEntity($entity);

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity();

    /**
     * Set value
     *
     * @param string $value
     * @return SyliusSearchIndex
     */
    public function setValue($value);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * @param $tags
     *
     * @return $this
     */
    public function setTags($tags);

    /**
     * Get value
     *
     * @return string
     */
    public function getTags();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SyliusSearchIndex
     */
    public function setCreatedAt($createdAt);

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();

} 