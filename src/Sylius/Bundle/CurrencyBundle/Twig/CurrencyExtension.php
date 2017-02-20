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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var CurrencyHelperInterface
     */
    private $helper;

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
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_currency_symbol', [$this->helper, 'convertCurrencyCodeToSymbol']),
        ];
    }
}
