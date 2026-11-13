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

namespace Tests\Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Grid\Filter\ProductTaxonFilter;
use Sylius\Bundle\ShopBundle\Grid\ProductGridInterface;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\GridComponent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\UX\LiveComponent\LiveResponder;

#[CoversClass(GridComponent::class)]
final class GridComponentTest extends TestCase
{
    private ChannelInterface&Stub $channel;

    private Stub&TaxonRepositoryInterface $taxonRepository;

    private LiveResponder $liveResponder;

    private ?Grid $renderedGrid = null;

    private ?Parameters $renderedParameters = null;

    private GridComponent $component;

    protected function setUp(): void
    {
        $this->channel = $this->createStub(ChannelInterface::class);

        $channelContext = $this->createStub(ChannelContextInterface::class);
        $channelContext->method('getChannel')->willReturn($this->channel);

        $localeContext = $this->createStub(LocaleContextInterface::class);
        $localeContext->method('getLocaleCode')->willReturn('en_US');

        $this->taxonRepository = $this->createStub(TaxonRepositoryInterface::class);

        $gridProvider = $this->createStub(GridProviderInterface::class);
        $gridProvider->method('get')->willReturnCallback(fn (string $code): Grid => $this->createGrid($code));

        $gridViewFactory = $this->createStub(GridViewFactoryInterface::class);
        $gridViewFactory->method('create')->willReturnCallback(function (Grid $grid, Parameters $parameters): GridView {
            $this->renderedGrid = $grid;
            $this->renderedParameters = $parameters;

            return new GridView(new Pagerfanta(new FixedAdapter(100, [])), $grid, $parameters);
        });

        $this->liveResponder = new LiveResponder();

        $this->component = new GridComponent($channelContext, $localeContext, $this->taxonRepository, true);
        $this->component->setGridProvider($gridProvider);
        $this->component->setGridViewFactory($gridViewFactory);
        $this->component->setLiveResponder($this->liveResponder);
    }

    #[Test]
    public function it_injects_the_repository_arguments_into_the_grid(): void
    {
        $taxon = $this->createStub(TaxonInterface::class);
        $this->component->taxon = $taxon;
        $this->component->sorting = ['price' => 'asc'];

        $this->component->getGridView();

        $this->assertSame(ProductGridInterface::NAME, $this->renderedGrid?->getCode());
        $this->assertSame([
            'channel' => $this->channel,
            'locale' => 'en_US',
            'sorting' => ['price' => 'asc'],
            'taxon' => $taxon,
            'includeAllDescendants' => true,
        ], $this->renderedGrid->getDriverConfiguration()['repository']['arguments']);
        $this->assertSame('createShopListQueryBuilderForChannel', $this->renderedGrid->getDriverConfiguration()['repository']['method']);
    }

    #[Test]
    public function it_uses_the_first_grid_limit_by_default(): void
    {
        $data = $this->component->getGridView()->getData();

        $this->assertInstanceOf(Pagerfanta::class, $data);
        $this->assertSame(9, $data->getMaxPerPage());
        $this->assertSame(1, $data->getCurrentPage());
    }

    #[Test]
    public function it_uses_the_requested_limit_and_page(): void
    {
        $this->component->limit = 18;
        $this->component->page = 3;

        $data = $this->component->getGridView()->getData();

        $this->assertInstanceOf(Pagerfanta::class, $data);
        $this->assertSame(18, $data->getMaxPerPage());
        $this->assertSame(3, $data->getCurrentPage());
    }

    #[Test]
    public function it_ignores_limits_and_pages_that_are_not_allowed(): void
    {
        $this->component->limit = 100000;
        $this->component->page = -2;

        $data = $this->component->getGridView()->getData();

        $this->assertInstanceOf(Pagerfanta::class, $data);
        $this->assertSame(9, $data->getMaxPerPage());
        $this->assertSame(1, $data->getCurrentPage());
        $this->assertNull($this->component->limit);
        $this->assertNull($this->component->page);
    }

    #[Test]
    public function it_ignores_invalid_sorting(): void
    {
        $this->component->sorting = [
            'name' => 'DESC',
            'price' => 'sideways',
            'unknown' => 'asc',
            'notSortable' => 'asc',
            'position' => 'asc',
        ];

        $this->component->getGridView();

        $this->assertSame(['name' => 'desc'], $this->component->sorting);
        $this->assertSame(['name' => 'desc'], $this->renderedParameters?->get('sorting'));
    }

    #[Test]
    public function it_allows_sorting_by_position_within_a_taxon(): void
    {
        $this->component->taxon = $this->createStub(TaxonInterface::class);
        $this->component->sorting = ['position' => 'asc'];

        $this->component->getGridView();

        $this->assertSame(['position' => 'asc'], $this->component->sorting);
    }

    #[Test]
    public function it_ignores_the_default_sorting_by_position_outside_a_taxon(): void
    {
        $this->component->getGridView();

        $this->assertSame([], $this->renderedGrid?->getSorting());
    }

    #[Test]
    public function it_keeps_the_default_sorting_by_position_within_a_taxon(): void
    {
        $this->component->taxon = $this->createStub(TaxonInterface::class);

        $this->component->getGridView();

        $this->assertSame(['position' => 'asc'], $this->renderedGrid?->getSorting());
    }

    #[Test]
    public function it_exposes_the_active_filters(): void
    {
        $caps = $this->createStub(TaxonInterface::class);
        $caps->method('getCode')->willReturn('caps');
        $caps->method('getName')->willReturn('Caps');
        $this->taxonRepository->method('findBy')->willReturn([$caps]);

        $this->component->criteria = [
            'search' => ['value' => 'blue', 'type' => 'contains'],
            'taxon' => ['caps'],
        ];

        $this->assertSame([
            ['filter' => 'search', 'label' => 'sylius.ui.search_products', 'value' => 'blue', 'value_label' => 'blue'],
            ['filter' => 'taxon', 'label' => 'sylius.ui.category', 'value' => 'caps', 'value_label' => 'Caps'],
        ], $this->component->getActiveFilters());
    }

    #[Test]
    public function it_does_not_expose_empty_filters(): void
    {
        $this->component->criteria = [
            'search' => ['value' => '  '],
            'taxon' => [],
        ];

        $this->assertSame([], $this->component->getActiveFilters());
    }

    #[Test]
    public function it_removes_a_value_of_a_multiple_filter(): void
    {
        $this->component->page = 4;
        $this->component->criteria = [
            'search' => ['value' => 'blue'],
            'taxon' => ['caps', 't_shirts'],
        ];

        $this->component->removeFilterValue('taxon', 'caps');

        $this->assertSame(['search' => ['value' => 'blue'], 'taxon' => ['t_shirts']], $this->component->criteria);
        $this->assertSame(1, $this->component->page);
        $this->assertSame([[
            'event' => 'criteriaChanged',
            'data' => ['criteria' => ['search' => ['value' => 'blue'], 'taxon' => ['t_shirts']]],
            'target' => null,
            'componentName' => null,
        ]], $this->liveResponder->getEventsToEmit());
    }

    #[Test]
    public function it_removes_a_whole_filter(): void
    {
        $this->component->criteria = [
            'search' => ['value' => 'blue'],
            'taxon' => ['caps'],
        ];

        $this->component->removeFilterValue('search', 'blue');
        $this->assertSame(['taxon' => ['caps']], $this->component->criteria);

        $this->component->removeFilterValue('taxon', 'caps');
        $this->assertNull($this->component->criteria);
    }

    #[Test]
    public function it_removes_the_filters_hidden_within_a_taxon(): void
    {
        $this->component->taxon = $this->createStub(TaxonInterface::class);
        $this->component->criteria = [
            'search' => ['value' => 'blue'],
            'taxon' => ['caps'],
        ];

        $this->assertSame(['search'], array_keys($this->component->getFilters()));
        $this->assertSame([
            ['filter' => 'search', 'label' => 'sylius.ui.search_products', 'value' => 'blue', 'value_label' => 'blue'],
        ], $this->component->getActiveFilters());

        $this->component->getGridView();

        $this->assertFalse($this->renderedGrid?->hasFilter('taxon'));
        $this->assertSame(['search' => ['value' => 'blue']], $this->component->criteria);
        $this->assertSame(['search' => ['value' => 'blue']], $this->renderedParameters?->get('criteria'));
    }

    #[Test]
    public function it_keeps_the_filters_hidden_within_a_taxon_when_listing_all_products(): void
    {
        $this->component->criteria = ['taxon' => ['caps']];

        $this->assertSame(['search', 'taxon'], array_keys($this->component->getFilters()));

        $this->component->getGridView();

        $this->assertSame(['taxon' => ['caps']], $this->renderedParameters?->get('criteria'));
    }

    #[Test]
    public function it_hydrates_and_dehydrates_the_taxon(): void
    {
        $taxon = $this->createStub(TaxonInterface::class);
        $taxon->method('getId')->willReturn(42);
        $this->taxonRepository->method('find')->willReturnCallback(static fn (mixed $id): ?TaxonInterface => 42 === $id ? $taxon : null);

        $this->assertSame(42, $this->component->dehydrateTaxon($taxon));
        $this->assertNull($this->component->dehydrateTaxon(null));
        $this->assertSame($taxon, $this->component->hydrateTaxon(42));
        $this->assertNull($this->component->hydrateTaxon(null));
    }

    private function createGrid(string $code): Grid
    {
        $grid = Grid::fromCodeAndDriverConfiguration($code, 'doctrine/orm', [
            'class' => 'App\Entity\Product',
            'repository' => [
                'method' => 'createShopListQueryBuilderForChannel',
                'arguments' => [],
            ],
        ]);
        $grid->setLimits([9, 18, 27]);
        $grid->setSorting(['position' => 'asc']);

        foreach (['name' => 'translation.name', 'price' => 'channelPricing.price', 'position' => 'productTaxon.position'] as $name => $path) {
            $field = Field::fromNameAndType($name, 'string');
            $field->setSortable($path);
            $grid->addField($field);
        }
        $grid->addField(Field::fromNameAndType('notSortable', 'string'));

        $search = Filter::fromNameAndType('search', 'shop_string');
        $search->setLabel('sylius.ui.search_products');
        $grid->addFilter($search);

        $taxon = Filter::fromNameAndType('taxon', ProductTaxonFilter::TYPE);
        $taxon->setLabel('sylius.ui.category');
        $taxon->setOptions([GridComponent::HIDDEN_WITHIN_TAXON_OPTION => true]);
        $grid->addFilter($taxon);

        return $grid;
    }
}
