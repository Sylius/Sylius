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

use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;

/**
 * Sylius money Twig helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyExtension extends \Twig_Extension
{
    /**
     * @var MoneyHelperInterface
     */
    protected $helper;

    /**
     * @param MoneyHelperInterface $helper
     */
    public function __construct(MoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_money', [$this->helper, 'formatAmount']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
