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

namespace Tests\Sylius\Bundle\ShopBundle\Twig\Component\Product\Grid;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Grid\Filter\ProductTaxonFilter;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Grid\TaxonFilterComponent;
use Sylius\Component\Grid\Definition\Filter;
use Symfony\UX\LiveComponent\LiveResponder;

#[CoversClass(TaxonFilterComponent::class)]
final class TaxonFilterComponentTest extends TestCase
{
    private LiveResponder $liveResponder;

    private TaxonFilterComponent $component;

    protected function setUp(): void
    {
        $this->liveResponder = new LiveResponder();

        $this->component = new TaxonFilterComponent();
        $this->component->setLiveResponder($this->liveResponder);
        $this->component->filter = Filter::fromNameAndType('taxon', ProductTaxonFilter::TYPE);
    }

    #[Test]
    public function it_initializes_its_value_from_the_grid_criteria(): void
    {
        $this->component->criteria = ['taxon' => ['caps', '', 't_shirts']];
        $this->component->initializeFilterValue();
        $this->assertSame(['caps', 't_shirts'], $this->component->value);

        $this->component->criteria = ['taxon' => 'caps'];
        $this->component->initializeFilterValue();
        $this->assertSame(['caps'], $this->component->value);

        $this->component->criteria = null;
        $this->component->initializeFilterValue();
        $this->assertSame([], $this->component->value);
    }

    #[Test]
    public function it_notifies_the_grid_when_its_value_changes(): void
    {
        $this->component->value = ['caps'];

        $this->component->onChange();

        $this->assertSame([[
            'event' => 'filterChanged',
            'data' => ['newCriteria' => ['taxon' => ['caps']]],
            'target' => 'up',
            'componentName' => null,
        ]], $this->liveResponder->getEventsToEmit());
    }

    #[Test]
    public function it_can_be_cleared(): void
    {
        $this->component->value = ['caps'];

        $this->component->clear();

        $this->assertSame([], $this->component->value);
        $this->assertSame(['newCriteria' => ['taxon' => []]], $this->liveResponder->getEventsToEmit()[0]['data']);
    }

    #[Test]
    public function it_is_synchronized_with_the_grid_criteria(): void
    {
        $this->component->value = ['caps', 't_shirts'];

        $this->component->onCriteriaChanged(['taxon' => ['t_shirts']]);

        $this->assertSame(['taxon' => ['t_shirts']], $this->component->criteria);
        $this->assertSame(['t_shirts'], $this->component->value);
    }
}
