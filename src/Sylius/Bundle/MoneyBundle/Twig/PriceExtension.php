<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Twig;

use Sylius\Bundle\MoneyBundle\Templating\Helper\PriceHelperInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PriceExtension extends \Twig_Extension
{
    /**
     * @var PriceHelperInterface
     */
    private $helper;

    /**
     * @param PriceHelperInterface $helper
     */
    public function __construct(PriceHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_price', [$this->helper, 'convertAndFormatAmount']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price';
    }
}
