<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class CreatePromotionRuleEvent extends Event
{
    private Proxy|PromotionRuleInterface|null $promotionRule = null;

    public function __construct(private array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getPromotionRule(): Proxy|PromotionRuleInterface
    {
        Assert::notNull($this->promotionRule, 'Promotion rule has not been created.');

        return $this->promotionRule;
    }

    public function setPromotionRule(Proxy|PromotionRuleInterface $promotionRule): void
    {
        $this->promotionRule = $promotionRule;
    }
}
