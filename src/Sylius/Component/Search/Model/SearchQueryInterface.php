<?php

declare(strict_types=1);

namespace Sylius\Component\Search\Model;

interface SearchQueryInterface
{
    public function getTerms(): string;

    public function getLocaleCode(): string;

    public function getPageNumber(string $type): int;
}
