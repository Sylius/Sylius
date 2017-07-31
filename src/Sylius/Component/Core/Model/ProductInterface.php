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

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Review\Model\ReviewableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
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
    public function getVariantSelectionMethod();

    /**
     * @param string $variantSelectionMethod
     *
     * @throws \InvalidArgumentException
     */
    public function setVariantSelectionMethod($variantSelectionMethod);

    /**
     * @return bool
     */
    public function isVariantSelectionMethodChoice();

    /**
     * @return string
     */
    public function getVariantSelectionMethodLabel();

    /**
     * @return string
     */
    public function getShortDescription();

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription);

    /**
     * @return TaxonInterface
     */
    public function getMainTaxon();

    /**
     * @param TaxonInterface $mainTaxon
     */
    public function setMainTaxon(TaxonInterface $mainTaxon = null);

    /**
     * @return ReviewInterface[]
     */
    public function getAcceptedReviews();
}
