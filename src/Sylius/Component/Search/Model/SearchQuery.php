<?php

declare(strict_types=1);

namespace Sylius\Component\Search\Model;

use Symfony\Component\HttpFoundation\ParameterBag;

class SearchQuery implements SearchQueryInterface
{
    /** @var string */
    private $locale;

    /** @var string */
    private $terms;

    /** @var ParameterBag */
    private $parameterBag;

    public function __construct(string $terms, string $locale, ParameterBag $parameterBag)
    {
        $this->terms = $terms;
        $this->locale = $locale;
        $this->parameterBag = $parameterBag;
    }

    public function getTerms(): string
    {
        return $this->terms;
    }

    public function getLocaleCode(): string
    {
        return $this->locale;
    }

    public function getPageNumber(string $type): int
    {
        return (int) $this->parameterBag->get(sprintf('page_%s', $type), 1);
    }
}
