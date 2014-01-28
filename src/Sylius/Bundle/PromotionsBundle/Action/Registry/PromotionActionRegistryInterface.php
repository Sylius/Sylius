<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Action\Registry;

use Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface;

/**
 * Promotion action registry interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionActionRegistryInterface
{
    /**
     * @return PromotionActionInterface[]
     */
    public function getActions();

    /**
     * @param string                   $name
     * @param PromotionActionInterface $action
     */
    public function registerAction($name, PromotionActionInterface $action);

    /**
     * @param string $name
     */
    public function unregisterAction($name);

    /**
     * @param string $name
     *
     * @return Boolean
     */
    public function hasAction($name);

    /**
     * @param string $name
     *
     * @return PromotionActionInterface
     */
    public function getAction($name);
}
