<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;


interface PricingElementInterface
{
    public function getPriceForChannel(string $channelName): string;

    public function getOrginalPriceForChannel(string $channelName): string;
}
