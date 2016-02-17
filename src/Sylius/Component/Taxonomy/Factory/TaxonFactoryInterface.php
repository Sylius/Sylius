<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Factory;

use Sylius\Component\Translation\Factory\TranslatableFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxonFactoryInterface extends TranslatableFactoryInterface
{
    /**
     * @param mixed $promotionId
     *
     * @return TaxonInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createForTaxonomy($taxonomyId);
}
