<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Twig;

use Sylius\Bundle\GridBundle\Templating\Helper\EnabledItemsHelper;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class EnabledItemsExtension extends \Twig_Extension
{
    /**
     * @var EnabledItemsHelper
     */
    private $enabledItemsHelper;

    /**
     * @param EnabledItemsHelper $enabledItemsHelper
     */
    public function __construct(EnabledItemsHelper $enabledItemsHelper)
    {
        $this->enabledItemsHelper = $enabledItemsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sylius_enabled_items', [$this->enabledItemsHelper, 'getEnabledItems']),
        );
    }
}
