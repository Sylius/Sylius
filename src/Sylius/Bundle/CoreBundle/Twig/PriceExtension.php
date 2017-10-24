<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Twig;

use Symfony\Component\Templating\Helper\Helper;

final class PriceExtension extends \Twig_Extension
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('sylius_calculate_price', [$this->helper, 'getPrice']),
        ];
    }
}
