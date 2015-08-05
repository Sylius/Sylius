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
 * Default model implementation of CustomizationSubjectInterface.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CustomizationSubject implements CustomizationSubjectInterface
{
    /**
     * Identifier
     *
     * @var integer
     */
    protected $id;

    /**
     * Product customizations.
     *
     * @var ArrayCollection|CustomizationInterface[]
     */
    protected $customizations;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->customizations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomizations()
    {
        return $this->customizations;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomization(CustomizationInterface $customization)
    {
        if (!$this->hasCustomization($customization)) {
            $this->customizations->add($customization);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeCustomization(CustomizationInterface $customization)
    {
        if ($this->hasCustomization($customization)) {
            $this->customizations->removeElement($customization);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCustomization(CustomizationInterface $customization)
    {
        return $this->customizations->contains($customization);
    }
}
