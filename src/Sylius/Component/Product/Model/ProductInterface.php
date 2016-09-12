<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Association\Model\AssociableInterface;
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ProductInterface extends
    AttributeSubjectInterface,
    SlugAwareInterface,
    TimestampableInterface,
    ToggleableInterface,
    ProductTranslationInterface,
    AssociableInterface,
    CodeAwareInterface,
    TranslatableInterface
{
    /**
     * @return bool
     */
    public function hasVariants();
    /**
     * @return Collection|VariantInterface[]
     */
    public function getVariants();
    /**
     * @param Collection $variants
     */
    public function setVariants(Collection $variants);
    /**
     * @param VariantInterface $variant
     */
    public function addVariant(VariantInterface $variant);
    /**
     * @param VariantInterface $variant
     */
    public function removeVariant(VariantInterface $variant);
    /**
     * @param VariantInterface $variant
     *
     * @return bool
     */
    public function hasVariant(VariantInterface $variant);
    /**
     * @return bool
     */
    public function hasOptions();
    /**
     * @return Collection|OptionInterface[]
     */
    public function getOptions();
    /**
     * @param Collection $options
     */
    public function setOptions(Collection $options);
    /**
     * @param OptionInterface $option
     */
    public function addOption(OptionInterface $option);
    /**
     * @param OptionInterface $option
     */
    public function removeOption(OptionInterface $option);
    /**
     * @param OptionInterface $option
     *
     * @return bool
     */
    public function hasOption(OptionInterface $option);

    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @return \DateTime
     */
    public function getAvailableOn();

    /**
     * @param null|\DateTime $availableOn
     */
    public function setAvailableOn(\DateTime $availableOn = null);

    /**
     * @return \DateTime
     */
    public function getAvailableUntil();

    /**
     * @param null|\DateTime $availableUntil
     */
    public function setAvailableUntil(\DateTime $availableUntil = null);

    /**
     * @param ProductAssociationInterface $association
     */
    public function addAssociation(ProductAssociationInterface $association);

    /**
     * @return ProductAssociationInterface[]
     */
    public function getAssociations();

    /**
     * @param ProductAssociationInterface $association
     */
    public function removeAssociation(ProductAssociationInterface $association);

    /**
     * @return bool
     */
    public function isSimple();
}
