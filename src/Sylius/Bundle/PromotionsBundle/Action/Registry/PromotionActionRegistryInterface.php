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
    public function getActions();
    public function registerAction($name, PromotionActionInterface $action);
    public function unregisterAction($name);
    public function hasAction($name);
    public function getAction($name);
}
