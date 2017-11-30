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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ProductTaxonInterface extends ResourceInterface
{
    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface;

    /**
     * @param ProductInterface|null $product
     */
    public function setProduct(?ProductInterface $product): void;

    /**
     * @return TaxonInterface|null
     */
    public function getTaxon(): ?TaxonInterface;

    /**
     * @param TaxonInterface|null $taxon
     */
    public function setTaxon(?TaxonInterface $taxon): void;

    /**
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void;
}
