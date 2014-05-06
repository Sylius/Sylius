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

use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper;

/**
 * Sylius currency Twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var CurrencyHelper
     */
    protected $helper;

    /**
     * @param CurrencyHelper $helper
     */
    public function __construct(CurrencyHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sylius_currency', array($this, 'convertAmount')),
        );
    }

    /**
     * Convert amount to target currency.
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function convertAmount($amount, $currency = null)
    {
        return $this->helper->convertAmount($amount, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_currency';
    }
}
