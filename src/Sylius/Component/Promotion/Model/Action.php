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

use Sylius\Component\Resource\Model\Action as BaseAction;

/**
 * Promotion action model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Action extends BaseAction implements ActionInterface
{
    /**
     * The promotion associated with this action
     *
     * @var PromotionInterface
     */
    protected $promotion;

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
