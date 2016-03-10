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

use Sylius\Bundle\CoreBundle\Templating\Helper\RegularPriceHelper;
use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegularPriceExtension extends \Twig_Extension
{
    /**
     * @var RegularPriceHelper
     */
    private $regularPriceHelper;

    /**
     * @param RegularPriceHelper $regularPriceHelper
     */
    public function __construct(RegularPriceHelper $regularPriceHelper)
    {
        $this->regularPriceHelper = $regularPriceHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_regular_price', [$this, 'getItemRegularPrice']),
        ];
    }

    /**
     * @param OrderItemInterface $orderItem
     *
     * @return int
     */
    public function getItemRegularPrice(OrderItemInterface $orderItem)
    {
        return $this->regularPriceHelper->getRegularPrice($orderItem);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_regular_price';
    }
}
