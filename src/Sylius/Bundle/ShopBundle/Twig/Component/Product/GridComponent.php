<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\ShopBundle\Grid\Filter\TaxonFilter;
use Sylius\Bundle\ShopBundle\Grid\ProductGridInterface;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Twig\Component\ComponentWithGridTrait;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * Live product list, built on top of the "sylius_shop_product" grid (adapted by the LiveProductGridMutator).
 *
 * Page, criteria, sorting and limit are kept in the URL query string, using the same parameter names
 * as the regular grids, so that links to the list remain shareable.
 *
 * @experimental
 */
class GridComponent
{
    use ComponentWithGridTrait {
        getGridDefinition as private getBaseGridDefinition;
        getGridView as private getBaseGridView;
    }
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use TemplatePropTrait;

    /** Filter option removing the filter when products are listed within a taxon (e.g. a category filter). */
    public const HIDDEN_WITHIN_TAXON_OPTION = 'hidden_within_taxon';

    private const SORTING_DIRECTIONS = ['asc', 'desc'];

    /** When null, products from all taxons are listed. */
    #[LiveProp(hydrateWith: 'hydrateTaxon', dehydrateWith: 'dehydrateTaxon')]
    public ?TaxonInterface $taxon = null;

    private ?GridViewInterface $gridView = null;

    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(
        protected readonly ChannelContextInterface $channelContext,
        protected readonly LocaleContextInterface $localeContext,
        protected readonly TaxonRepositoryInterface $taxonRepository,
        protected readonly bool $includeAllDescendants = false,
        protected readonly string $gridName = ProductGridInterface::NAME,
    ) {
    }

    public function getGridView(): GridViewInterface
    {
        // The grid is exposed several times per render (grid, filters, active filters...), build it only once
        if (null !== $this->gridView) {
            return $this->gridView;
        }

        $this->sanitizeParameters();

        $gridView = $this->getBaseGridView();
        $data = $gridView->getData();

        if ($data instanceof PagerfantaInterface) {
            $data->setMaxPerPage($this->limit ?? $this->getDefaultLimit());
            $data->setCurrentPage($this->page ?? 1);
        }

        return $this->gridView = $gridView;
    }

    /**
     * @return list<array{filter: string, label: string|bool|null, value: string, value_label: string}>
     */
    #[ExposeInTemplate('active_filters')]
    public function getActiveFilters(): array
    {
        $activeFilters = [];

        foreach ($this->getFilters() as $filter) {
            $criterion = $this->criteria[$filter->getName()] ?? null;

            foreach ($this->getActiveFilterValues($filter, $criterion) as $value => $valueLabel) {
                $activeFilters[] = [
                    'filter' => $filter->getName(),
                    'label' => $filter->getLabel(),
                    'value' => (string) $value,
                    'value_label' => $valueLabel,
                ];
            }
        }

        return $activeFilters;
    }

    public function hydrateTaxon(mixed $value): ?TaxonInterface
    {
        if (null === $value || '' === $value) {
            return null;
        }

        /** @var TaxonInterface|null $taxon */
        $taxon = $this->taxonRepository->find($value);

        return $taxon;
    }

    public function dehydrateTaxon(?TaxonInterface $taxon): mixed
    {
        return $taxon?->getId();
    }

    #[LiveAction]
    public function removeFilterValue(#[LiveArg] string $filter, #[LiveArg] ?string $value = null): void
    {
        $criteria = $this->criteria ?? [];
        $criterion = $criteria[$filter] ?? null;

        if (is_array($criterion) && null !== $value && array_is_list($criterion)) {
            $criterion = array_values(array_filter(
                $criterion,
                static fn (mixed $currentValue): bool => $currentValue !== $value,
            ));
        } else {
            $criterion = null;
        }

        if (null === $criterion || [] === $criterion) {
            unset($criteria[$filter]);
        } else {
            $criteria[$filter] = $criterion;
        }

        // Also notifies the filter components, so they are synchronized with the new criteria
        $this->changeCriteria([] === $criteria ? null : $criteria);
    }

    protected function getGridName(): string
    {
        return $this->gridName;
    }

    /** @return array<string, mixed> */
    protected function getRepositoryArguments(): array
    {
        return [
            'channel' => $this->channelContext->getChannel(),
            'locale' => $this->localeContext->getLocaleCode(),
            'sorting' => $this->sorting ?? [],
            'taxon' => $this->taxon,
            'includeAllDescendants' => $this->includeAllDescendants,
        ];
    }

    /**
     * @return array<array-key, string> values indexed by their raw value
     */
    protected function getActiveFilterValues(Filter $filter, mixed $criterion): array
    {
        if (null === $criterion || '' === $criterion || [] === $criterion) {
            return [];
        }

        if (TaxonFilter::TYPE === $filter->getType()) {
            $codes = array_filter((array) $criterion, static fn (mixed $code): bool => is_string($code) && '' !== $code);
            if ([] === $codes) {
                return [];
            }

            $values = [];
            /** @var TaxonInterface $taxon */
            foreach ($this->taxonRepository->findBy(['code' => array_values($codes)]) as $taxon) {
                $values[(string) $taxon->getCode()] = (string) $taxon->getName();
            }

            return $values;
        }

        // String like filters (e.g. "shop_string")
        if (is_array($criterion) && array_key_exists('value', $criterion)) {
            $criterion = $criterion['value'];
        }

        if (is_scalar($criterion) && '' !== trim((string) $criterion)) {
            return [(string) $criterion => (string) $criterion];
        }

        return [];
    }

    /**
     * Overrides the grid definition of ComponentWithGridTrait to inject the repository arguments
     * from the component state (e.g. the taxon given by the controller, the sorting kept in the URL).
     */
    private function getGridDefinition(): Grid
    {
        $grid = $this->getBaseGridDefinition();

        $driverConfiguration = $grid->getDriverConfiguration();
        $driverConfiguration['repository']['arguments'] = $this->getRepositoryArguments();
        $grid->setDriverConfiguration($driverConfiguration);

        // Default sortings that cannot be applied (e.g. by position outside a taxon) would break the query
        $grid->setSorting(array_filter(
            $grid->getSorting(),
            fn (int|string $field): bool => $this->isSortableField($grid, (string) $field),
            \ARRAY_FILTER_USE_KEY,
        ));

        if (null !== $this->taxon) {
            foreach ($grid->getFilters() as $filter) {
                if (true === ($filter->getOptions()[self::HIDDEN_WITHIN_TAXON_OPTION] ?? false)) {
                    $grid->removeFilter($filter->getName());
                }
            }
        }

        return $grid;
    }

    /**
     * Prevents invalid values coming from the URL (or from the browser) to break the grid
     * or to load a huge number of products at once.
     */
    private function sanitizeParameters(): void
    {
        $grid = $this->getGridDefinition();

        if (null !== $this->page && $this->page < 1) {
            $this->page = null;
        }

        if (null !== $this->limit && !in_array($this->limit, $grid->getLimits(), true)) {
            $this->limit = null;
        }

        // Criteria of removed filters (e.g. hidden within a taxon) would still be applied by the grid
        if (null !== $this->criteria) {
            $criteria = array_filter(
                $this->criteria,
                static fn (int|string $name): bool => $grid->hasFilter((string) $name),
                \ARRAY_FILTER_USE_KEY,
            );
            $this->criteria = [] === $criteria ? null : $criteria;
        }

        if (null === $this->sorting) {
            return;
        }

        $sorting = [];
        foreach ($this->sorting as $field => $direction) {
            if (!is_string($field) || !is_string($direction) || !$this->isSortableField($grid, $field)) {
                continue;
            }

            $direction = strtolower($direction);
            if (!in_array($direction, self::SORTING_DIRECTIONS, true)) {
                continue;
            }

            $sorting[$field] = $direction;
        }

        $this->sorting = [] === $sorting ? null : $sorting;
    }

    private function isSortableField(Grid $grid, string $field): bool
    {
        if (!$grid->hasField($field) || null === $grid->getField($field)->getSortable()) {
            return false;
        }

        // Products can only be sorted by position within a taxon
        return 'position' !== $field || null !== $this->taxon;
    }

    private function getDefaultLimit(): int
    {
        $limits = $this->getGridDefinition()->getLimits();

        return (int) (reset($limits) ?: 10);
    }
}
