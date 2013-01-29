<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Model;

/**
 * Tax category interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxCategoryInterface
{
    /**
     * Get category identifier.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get category name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get all applicable tax rates.
     *
     * @return Collection
     */
    public function getRates();

    /**
     * Add a rate to this category.
     *
     * @param TaxRateInterface $rate
     */
    public function addRate(TaxRateInterface $rate);

    /**
     * Remove rate from this category.
     *
     * @param TaxRateInterface $rate
     */
    public function removeRate(TaxRateInterface $rate);

    /**
     * Has rate?
     *
     * @param TaxableInterface $rate
     *
     * @return Boolean
     */
    public function hasRate(TaxRateInterface $rate);

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Set creation time.
     *
     * @return DateTime
     */
    public function getUpdatedAt();
}
