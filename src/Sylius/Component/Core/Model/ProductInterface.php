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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

interface ProductInterface extends
    BaseProductInterface,
    ProductTaxonsAwareInterface,
    ChannelsAwareInterface,
    ReviewableInterface,
    ImagesAwareInterface
{
    /*
     * Variant selection methods.
     *
     * 1) Choice - A list of all variants is displayed to user.
     *
     * 2) Match  - Each product option is displayed as select field.
     *             User selects the values and we match them to variant.
     */
    public const VARIANT_SELECTION_CHOICE = 'choice';
    public const VARIANT_SELECTION_MATCH = 'match';

    /**
     * @return string
     */
    public function getVariantSelectionMethod(): string;

    /**
     * @param string|null $variantSelectionMethod
     *
     * @throws \InvalidArgumentException
     */
    public function setVariantSelectionMethod(?string $variantSelectionMethod): void;

    /**
     * @return bool
     */
    public function isVariantSelectionMethodChoice(): bool;

    /**
     * @return string
     */
    public function getVariantSelectionMethodLabel(): string;

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string;

    /**
     * @param string|null $shortDescription
     */
    public function setShortDescription(?string $shortDescription): void;

    /**
     * @return TaxonInterface|null
     */
    public function getMainTaxon(): ?TaxonInterface;

    /**
     * @param TaxonInterface|null $mainTaxon
     */
    public function setMainTaxon(?TaxonInterface $mainTaxon): void;

    /**
     * @return Collection|ReviewInterface[]
     */
    public function getAcceptedReviews(): Collection;
}
