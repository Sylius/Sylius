<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class VariantPriceExtension extends AbstractExtension
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var MoneyFormatterInterface */
    private $moneyFormater;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        MoneyFormatterInterface $moneyFormater
    ){
        $this->channelRepository = $channelRepository;
        $this->moneyFormater = $moneyFormater;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_variant_price', [$this, 'getPriceWithCurrency']),
        ];
    }

    public function getPriceWithCurrency(int $price, string $channelCode): string
    {
        /** @var string $currencyCode */
        $currencyCode = $this->channelRepository->findOneByCode($channelCode)->getBaseCurrency()->getCode();

        return $this->moneyFormater->format($price, $currencyCode);
    }
}
