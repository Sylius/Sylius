<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Checker;

use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class VariantsParityChecker implements VariantsParityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkParity(VariantInterface $variant, VariableInterface $variable)
    {
        Assert::same(
            count($variant->getOptions()),
            count($variable->getOptions()),
            'Number of set option values should be equal to number of available options.'
        );

        foreach ($variable->getVariants() as $existingVariant) {
            // This check is require, because this function has to look for any other different variant with same option values set
            if ($variant === $existingVariant) {
                continue;
            }

            if ($this->matchOptions($variant, $existingVariant)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param VariantInterface $variant
     * @param VariantInterface $existingVariant
     *
     * @return bool
     */
    private function matchOptions(VariantInterface $variant, VariantInterface $existingVariant)
    {
        foreach ($variant->getOptions() as $option) {
            if (!$existingVariant->hasOption($option)) {
               return false;
            }
        }

        return true;
    }
}
