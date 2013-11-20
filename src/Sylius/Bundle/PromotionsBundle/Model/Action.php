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
 * Promotion action model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Action implements ActionInterface
{
    /**
     * The id of this action
     *
     * @var integer
     */
    protected $id;

    /**
     * The type of this action
     *
     * @var string
     */
    protected $type;

    /**
     * The configuration of this action
     *
     * @var array
     */
    protected $configuration;

    /**
     * The promotion associated with this action
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
     * @return int
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
