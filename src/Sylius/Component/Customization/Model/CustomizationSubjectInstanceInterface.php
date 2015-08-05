<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customization\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Customization subject instance interface
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface CustomizationSubjectInstanceInterface
{
    /**
     * Returns all customization values.
     *
     * @return Collection|CustomizationValueInterface[]
     */
    public function getCustomizationValues();

    /**
     * Set all customization values
     */
    public function setCustomizationValues(ArrayCollection $customizationValues);

    /**
     * Adds customization value.
     *
     * @param CustomizationValueInterface $customizationValue
     */
    public function addCustomizationValue(CustomizationValueInterface $customizationValue);

    /**
     * Removes customization value from subject instance.
     *
     * @param CustomizationValueInterface $customizationValue
     */
    public function removeCustomizationValue(CustomizationValueInterface $customizationValue);

    /**
     * Checks whether subject instance has given customization value.
     *
     * @param CustomizationValueInterface $customizationValue
     *
     * @return Boolean
     */
    public function hasCustomizationValue(CustomizationValueInterface $customizationValue);

    /**
     * Get the customization subject attached to the current subject instance
     */
    public function getCustomizationSubject();

    /**
     * Get a customizationValue by the name of its customization.
     *
     * @param $name
     *
     * @return CustomizationValue|null
     */
    public function getCustomizationValueByName($name);
}
