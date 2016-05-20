<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Twig;

use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface;

/**
 * Sylius currency Twig helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var CurrencyHelperInterface
     */
    protected $helper;

    /**
     * @param CurrencyHelperInterface $helper
     */
    public function __construct(CurrencyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_currency_symbol', [$this->helper, 'getBaseCurrencySymbol']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_currency', [$this->helper, 'convertAmount']),
            new \Twig_SimpleFilter('sylius_price', [$this->helper, 'convertAndFormatAmount']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency';
    }
}
