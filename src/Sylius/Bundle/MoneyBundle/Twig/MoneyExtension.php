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

use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelper;

/**
 * Sylius money Twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MoneyExtension extends \Twig_Extension
{
    /**
     * @var MoneyHelper
     */
    protected $helper;

    /**
     * @param MoneyHelper $helper
     */
    public function __construct(MoneyHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sylius_money', array($this, 'formatMoney')),
            new \Twig_SimpleFilter('sylius_price', array($this, 'formatPrice')),
        );
    }

    /**
     * Format the money amount to nice display form.
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function formatMoney($amount, $currency = null)
    {
        return $this->helper->formatMoney($amount, $currency);
    }

    /**
     * Convert price between currencies and format the amount to nice display form.
     *
     * @param integer     $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function formatPrice($amount, $currency = null)
    {
        return $this->helper->formatPrice($amount, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
