<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\MoneyBundle\Twig;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FormatMoneyExtension extends AbstractExtension
{
    public function __construct(private MoneyFormatterInterface $moneyFormatter)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_format_money', [$this->moneyFormatter, 'format']),
        ];
    }
}
