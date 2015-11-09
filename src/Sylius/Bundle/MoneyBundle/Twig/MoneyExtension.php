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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
            new \Twig_SimpleFilter('sylius_money', array($this, 'formatAmount')),
        );
    }

    /**
     * Format the money amount to nice display form.
     *
     * @param int         $amount
     * @param string|null $currency
     *
     * @return string
     */
    public function formatAmount($amount, $currency = null, $locale = null)
    {
        return $this->helper->formatAmount($amount, $currency, false, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
