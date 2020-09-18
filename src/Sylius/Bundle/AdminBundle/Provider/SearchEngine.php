<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Provider;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Search\Model\SearchQuery;
use Sylius\Component\Search\Provider\ResultSetProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class SearchEngine implements SearchEngineInterface
{
    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var ResultSetProviderInterface[] */
    private $resultSetProviders;

    public function __construct(
        LocaleContextInterface $localeContext,
        \Traversable $resultSetProviders
    ) {
        $this->localeContext = $localeContext;
        $this->resultSetProviders = iterator_to_array($resultSetProviders);
    }

    public function search(string $terms, ParameterBag $query): array
    {
        $searchQuery = new SearchQuery($terms, $this->localeContext->getLocaleCode(), $query);

        return array_map(function (ResultSetProviderInterface $resultSetProvider) use ($searchQuery) {
            return $resultSetProvider->getResultSet($searchQuery);
        }, $this->resultSetProviders);
    }
}
