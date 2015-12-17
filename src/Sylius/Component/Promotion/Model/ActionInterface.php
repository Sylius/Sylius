<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * An Action is now a grouping of Benefits and Filters, multiple of which can be applied to a single Promotion.
 *
 * e.g.: "Get $10 off suits or dresses and free shipping when you spend > $500"
 *
 * There are 2 actions here:
 *    1). $10 off suits or dresses - ($10 off is the Benefit, suits or dresses are Filters)
 *    2). Free Shipping - (Free Shipping Benefit, no filter)
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Pete Ward <peter.ward@reiss.com>
 */
interface ActionInterface extends ResourceInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return PromotionInterface
     */
    public function getPromotion();

    /**
     * @return ArrayCollection[FilterInterface]
     */
    public function getFilters();

    /**
     * @return ArrayCollection[BenefitInterface]
     */
    public function getBenefits();

    /**
     * @param PromotionInterface $promotion
     */
    public function setPromotion(PromotionInterface $promotion = null);

    /**
     * @param BenefitInterface $benefit
     *
     * @return boolean
     */
    public function hasBenefit(BenefitInterface $benefit);

    /**
     * @param BenefitInterface $benefit
     */
    public function addBenefit(BenefitInterface $benefit);

    /**
     * @param BenefitInterface $benefit
     */
    public function removeBenefit(BenefitInterface $benefit);

    /**
     * @param FilterInterface $filter
     *
     * @return boolean
     */
    public function hasFilter(FilterInterface $filter);

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter);

    /**
     * @param FilterInterface $filter
     */
    public function removeFilter(FilterInterface $filter);
}
