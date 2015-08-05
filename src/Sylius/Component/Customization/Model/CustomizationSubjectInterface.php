<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customization\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Customization subject interface.
 *
 * Should be implemented by models that support customizations.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface CustomizationSubjectInterface
{
    /**
     * Get available customizations.
     *
     * @return Collection|CustomizationInterface[]
     */
    public function getCustomizations();

    /**
     * Add a customization.
     *
     * @param CustomizationInterface $customization
     */
    public function addCustomization(CustomizationInterface $customization);

    /**
     * Remove customization from subject.
     *
     * @param CustomizationInterface $customization
     */
    public function removeCustomization(CustomizationInterface $customization);

    /**
     * Checks whether subject has given customization.
     *
     * @param CustomizationInterface $customization
     *
     * @return Boolean
     */
    public function hasCustomization(CustomizationInterface $customization);
}
