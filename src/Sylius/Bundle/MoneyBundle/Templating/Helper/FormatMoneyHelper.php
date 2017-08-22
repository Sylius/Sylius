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

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Symfony\Component\Templating\Helper\Helper;

class FormatMoneyHelper extends Helper implements FormatMoneyHelperInterface
{
    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @param MoneyFormatterInterface $moneyFormatter
     */
    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount(int $amount, string $currencyCode, string $localeCode): string
    {
        return $this->moneyFormatter->format($amount, $currencyCode, $localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_format_money';
    }
}
