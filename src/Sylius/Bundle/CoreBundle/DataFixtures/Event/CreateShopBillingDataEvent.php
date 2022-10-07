<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class CreateShopBillingDataEvent extends Event
{
    private Proxy|ShopBillingDataInterface|null $shopBillingData = null;

    public function __construct(private array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getShopBillingData(): Proxy|ProductInterface
    {
        Assert::notNull($this->shopBillingData, 'Shop billing data has not been created.');

        return $this->shopBillingData;
    }

    public function setShopBillingData(Proxy|ProductInterface $shopBillingData): void
    {
        $this->shopBillingData = $shopBillingData;
    }
}
