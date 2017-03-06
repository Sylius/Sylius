<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Templating\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class EnabledItemsHelper extends Helper
{
    /**
     * @param array $items
     * @param bool $enabled
     *
     * @return mixed
     */
    public function getEnabledItems(array $items, $enabled = true)
    {
        $itemsCollection = new ArrayCollection($items);

        return $itemsCollection->filter(
            function ($item) use ($enabled) {
                return ($item->isEnabled() === $enabled);
            }
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sylius_enabled_items';
    }
}
