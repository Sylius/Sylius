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

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Promotion\Model\FilterInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Action implements ActionInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var array
     */
    protected $configuration = array();

    /**
     * @var PromotionInterface
     */
    protected $promotion;

    /**
     * @var  ArrayCollection[BenefitInterface]
     */
    protected $benefits;

    /**
     * @var ArrayCollection[FilterInterface]
     */
    protected $filters;

    public function __construct()
    {
        $this->benefits = new ArrayCollection();
        $this->filters = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getType()
//    {
//        return $this->type;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function setType($type)
//    {
//        $this->type = $type;
//    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function setPromotion(PromotionInterface $promotion = null)
    {
        $this->promotion = $promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function getBenefits()
    {
        return $this->benefits;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilter(FilterInterface $filter)
    {
        return $this->filters->contains($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters->add($filter);
        $filter->setAction($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFilter(FilterInterface $filter)
    {
        $this->filters->removeElement($filter);
        $filter->unsetAction();
    }

    /**
     * {@inheritdoc}
     */
    public function hasBenefit(BenefitInterface $benefit)
    {
        return $this->benefits->contains($benefit);
    }

    /**
     * {@inheritdoc}
     */
    public function addBenefit(BenefitInterface $benefit)
    {
        $this->benefits->add($benefit);
        $benefit->setAction($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeBenefit(BenefitInterface $benefit)
    {
        $this->benefits->removeElement($benefit);
        $benefit->unsetAction();
    }

}
