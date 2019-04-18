<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Templating\Helper;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Templating\Helper\Helper;

class VariantPriceHelper extends Helper
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var MoneyFormatterInterface */
    private $moneyFormater;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        MoneyFormatterInterface $moneyFormater)
    {
        $this->channelRepository = $channelRepository;
        $this->moneyFormater = $moneyFormater;
    }

    public function getPriceWithCurrency(int $price, string $channelCode): string
    {
        /** @var CurrencyInterface $currencyCode */
        $currencyCode = $this->channelRepository->findOneByCode($channelCode)->getBaseCurrency()->getCode();

        return $this->moneyFormater->format($price, $currencyCode);
    }

    public function getName(): string
    {
        return 'variant_price_helper';
    }
}
