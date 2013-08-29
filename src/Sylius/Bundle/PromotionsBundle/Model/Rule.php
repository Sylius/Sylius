<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Model;

/**
 * Promotion rule model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Rule implements RuleInterface
{
    protected $id;
    protected $type;
    protected $configuration;
    protected $promotion;

    public function __construct()
    {
        $this->configuration = array();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getPromotion()
    {
        return $this->promotion;
    }

    public function setPromotion(PromotionInterface $promotion = null)
    {
        $this->promotion = $promotion;
    }
}
