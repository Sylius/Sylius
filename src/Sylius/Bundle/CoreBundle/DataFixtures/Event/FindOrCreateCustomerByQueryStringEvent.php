<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateCustomerByQueryStringEvent extends Event
{
    private Proxy|CustomerInterface|null $customer = null;

    public function __construct(private string $queryString)
    {
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getCustomer(): Proxy|CustomerInterface
    {
        Assert::notNull($this->customer, sprintf('Customer "%s" has not been found or created.', $this->queryString));

        return $this->customer;
    }

    public function setCustomer(Proxy|CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }
}
