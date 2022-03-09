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
use Sylius\Component\Promotion\Model\CatalogPromotionInterface as BaseCatalogPromotionInterface;

interface CatalogPromotionInterface extends BaseCatalogPromotionInterface, ChannelsAwareInterface
{
    public function getChannels(): Collection;
}
