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
    /**
     * Id
     *
     * @var integer
     */
    protected $id;

    /**
     * Type
     *
     * @var string
     */
    protected $type;

    /**
     * Configuration
     *
     * @var array
     */
    protected $configuration;

    /**
     * Associated promotion
     *
     * @var PromotionInterface
     */
    protected $promotion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configuration = array();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

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

        return $this;
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

        return $this;
    }
}
