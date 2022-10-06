<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateTaxonByQueryStringEvent extends Event
{
    private Proxy|TaxonInterface|null $taxon = null;

    public function __construct(private string $queryString)
    {
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getTaxon(): Proxy|ZoneInterface
    {
        Assert::notNull($this->taxon, sprintf('Taxon "%s" has not been found or created.', $this->queryString));

        return $this->taxon;
    }

    public function setTaxon(Proxy|ZoneInterface $taxon): void
    {
        $this->taxon = $taxon;
    }
}
