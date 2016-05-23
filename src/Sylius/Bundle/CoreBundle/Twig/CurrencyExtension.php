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

use Sylius\Bundle\CoreBundle\Templating\Helper\CurrencyHelper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var CurrencyHelper
     */
    private $currencyHelper;

    /**
     * @param CurrencyHelper $currencyHelper
     */
    public function __construct(CurrencyHelper $currencyHelper)
    {
        $this->currencyHelper = $currencyHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_price', [$this->currencyHelper, 'convertAndFormatAmount'])
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
