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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Customization Subject Instance.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CustomizationSubjectInstance implements CustomizationSubjectInstanceInterface
{
    /**
     * Identifier
     *
     * @var integer
     */
    protected $id;

    /**
     * Customization values.
     *
     * @var ArrayCollection|CustomizationValueInterface[]
     */
    protected $customizationValues;

    /**
     * Customization subject
     *
     * @var CustomizationSubjectInterface
     */
    protected $customizationSubject;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->customizationValues = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizationValues()
    {
        return $this->customizationValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizationSubject()
    {
        return $this->customizationSubject;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomizationValues(ArrayCollection $customizationValues)
    {
        foreach ($customizationValues as $customizationValue) {
            $customizationValue->setSubjectInstance($this);
        }

        $this->customizationValues = $customizationValues;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomizationValue(CustomizationValueInterface $customizationValue)
    {
        if (!$this->hasCustomizationValue($customizationValue)) {
            $this->customizationValues->add($customizationValue);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCustomizationValue(CustomizationValueInterface $customizationValue)
    {
        if ($this->hasCustomizationValue($customizationValue)) {
            $this->customizationValues->removeElement($customizationValue);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomizationValue(CustomizationValueInterface $customizationValue)
    {
        return $this->customizationValues->contains($customizationValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizationValueByName($name)
    {
        foreach ($this->customizationValues as $cv) {
            if ($name === $cv->getCustomization()->getName()) {
                return $cv;
            }
        }

        return null;
    }
}
