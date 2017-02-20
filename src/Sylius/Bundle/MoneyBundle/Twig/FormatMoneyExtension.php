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

use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class FormatMoneyExtension extends \Twig_Extension
{
    /**
     * @var FormatMoneyHelperInterface
     */
    private $helper;

    /**
     * @param FormatMoneyHelperInterface $helper
     */
    public function __construct(FormatMoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_format_money', [$this->helper, 'formatAmount']),
        ];
    }
}
