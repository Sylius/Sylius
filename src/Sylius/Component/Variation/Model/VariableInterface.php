<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Should be implemented by models that support variants and options.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface VariableInterface
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
}
