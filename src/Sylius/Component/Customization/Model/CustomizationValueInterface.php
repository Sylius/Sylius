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

/**
 * Customization value interface.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface CustomizationValueInterface
{
    /**
     * Get customization.
     *
     * @return CustomizationInterface
     */
    public function getCustomization();

    /**
     * Set customization.
     *
     * @param CustomizationInterface $customization
     */
    public function setCustomization(CustomizationInterface $customization);

    /**
     * Get internal value.
     *
     * @return string
     */
    public function getValue();

    /**
     * Set internal value.
     *
     * @param string $value
     */
    public function setValue($value);

    /**
     * Get subject instance
     *
     * @return CustomizationSubjectInstanceInterface
     */
    public function getSubjectInstance();

    /**
     * Set subject instance
     *
     * @param CustomizationSubjectInstanceInterface $subjectInstance
     */
    public function setSubjectInstance(CustomizationSubjectInstanceInterface $subjectInstance = null);
}
