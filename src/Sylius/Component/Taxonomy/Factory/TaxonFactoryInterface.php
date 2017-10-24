<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Taxonomy\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonFactoryInterface extends FactoryInterface
{
    /**
     * @param TaxonInterface $parent
     *
     * @return TaxonInterface
     */
    public function createForParent(TaxonInterface $parent): TaxonInterface;
}
