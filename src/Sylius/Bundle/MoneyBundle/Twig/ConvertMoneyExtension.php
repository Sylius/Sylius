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

namespace Sylius\Bundle\MoneyBundle\Twig;

use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ConvertMoneyExtension extends AbstractExtension
{
    /** @var ConvertMoneyHelperInterface */
    private $helper;

    public function __construct(ConvertMoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_convert_money', [$this->helper, 'convertAmount']),
        ];
    }
}
