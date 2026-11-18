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
use Sylius\Bundle\ShopBundle\Twig\Component\Product\Grid\StringFilterComponent;
use Sylius\Component\Grid\Definition\Filter;
use Symfony\UX\LiveComponent\LiveResponder;

#[CoversClass(StringFilterComponent::class)]
final class StringFilterComponentTest extends TestCase
{
    private LiveResponder $liveResponder;

    private StringFilterComponent $component;

    protected function setUp(): void
    {
        $this->liveResponder = new LiveResponder();

        $this->component = new StringFilterComponent();
        $this->component->setLiveResponder($this->liveResponder);
        $this->component->filter = Filter::fromNameAndType('search', 'shop_string');
    }

    #[Test]
    public function it_initializes_its_value_from_the_grid_criteria(): void
    {
        $this->component->criteria = ['search' => ['type' => 'contains', 'value' => 'cap']];
        $this->component->initializeFilterValue();
        $this->assertSame('cap', $this->component->value);

        $this->component->criteria = ['search' => 'cap'];
        $this->component->initializeFilterValue();
        $this->assertSame('cap', $this->component->value);

        $this->component->criteria = ['search' => ['value' => ['malformed']]];
        $this->component->initializeFilterValue();
        $this->assertSame('', $this->component->value);

        $this->component->criteria = null;
        $this->component->initializeFilterValue();
        $this->assertSame('', $this->component->value);
    }

    #[Test]
    public function it_notifies_the_grid_when_its_value_changes(): void
    {
        $this->component->value = 'cap';

        $this->component->onChange();

        $this->assertSame([[
            'event' => 'filterChanged',
            'data' => ['newCriteria' => ['search' => ['value' => 'cap']]],
            'target' => 'up',
            'componentName' => null,
        ]], $this->liveResponder->getEventsToEmit());
    }

    #[Test]
    public function it_is_cleared_when_the_filters_are_reset(): void
    {
        $this->component->criteria = ['search' => ['value' => 'cap']];
        $this->component->value = 'cap';

        $this->component->onFiltersReset();

        $this->assertSame('', $this->component->value);
        $this->assertNull($this->component->criteria);
    }
}
