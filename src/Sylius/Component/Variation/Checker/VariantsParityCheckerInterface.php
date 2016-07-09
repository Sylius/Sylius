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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface VariantsParityCheckerInterface
{
    /**
     * @param VariantInterface $variant
     * @param VariableInterface $variable
     *
     * @return bool
     */
    public function checkParity(VariantInterface $variant, VariableInterface $variable);
}
