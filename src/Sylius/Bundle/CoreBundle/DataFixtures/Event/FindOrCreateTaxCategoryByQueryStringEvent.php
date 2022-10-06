<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateTaxCategoryByQueryStringEvent extends Event
{
    private Proxy|TaxCategoryInterface|null $taxCategory = null;

    public function __construct(private string $queryString)
    {
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getTaxCategory(): Proxy|TaxCategoryInterface
    {
        Assert::notNull($this->taxCategory, sprintf('Tax category "%s" has not been found or created.', $this->queryString));

        return $this->taxCategory;
    }

    public function setTaxCategory(Proxy|TaxCategoryInterface $taxCategory): void
    {
        $this->taxCategory = $taxCategory;
    }
}
