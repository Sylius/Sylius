<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Templating\Helper\ChannelBasedPriceHelperInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelBasedPriceExtension extends \Twig_Extension
{
    /**
     * @var ChannelBasedPriceHelperInterface
     */
    private $helper;

    /**
     * @param ChannelBasedPriceHelperInterface $helper
     */
    public function __construct(ChannelBasedPriceHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_channel_variant_price', [$this->helper, 'getPriceForCurrentChannel']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_channel_variant_price';
    }
}
