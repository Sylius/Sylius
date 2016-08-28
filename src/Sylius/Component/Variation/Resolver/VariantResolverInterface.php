<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Variation\Resolver;

use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface VariantResolverInterface
{
    /**
     * @param VariableInterface $subject
     *
     * @return VariantInterface
     */
    public function getVariant(VariableInterface $subject);
}
