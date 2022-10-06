<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateProductByStringEvent extends Event
{
    private Proxy|ProductInterface|null $product = null;

    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getProduct(): Proxy|ProductInterface
    {
        Assert::notNull($this->product, sprintf('Product "%s" has not been found or created.', $this->code));

        return $this->product;
    }

    public function setProduct(Proxy|ProductInterface $product): void
    {
        $this->product = $product;
    }
}
