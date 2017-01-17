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

use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ConvertMoneyExtension extends \Twig_Extension
{
    /**
     * @var ConvertMoneyHelperInterface
     */
    private $helper;

    /**
     * @param ConvertMoneyHelperInterface $helper
     */
    public function __construct(ConvertMoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_convert_money', [$this->helper, 'convertAmount']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_convert_money';
    }
}
