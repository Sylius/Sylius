<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Generator;

use Sylius\Component\Variation\Model\VariableInterface;

/**
 * Interface for variant generating service.
 *
 * It is used to create all possible (non-existing) variations
 * of given object based on its options.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface VariantGeneratorInterface
{
    /**
     * Generate all possible variants if they don't exist currently.
     *
     * @param VariableInterface $variable
     */
    public function generate(VariableInterface $variable);
}
