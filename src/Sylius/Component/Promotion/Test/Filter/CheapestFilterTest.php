<?php

namespace Sylius\Component\Promotion\Test\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Filter\CheapestFilter;

class CheapestFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Subject under tests
     *
     * @var CheapestFilter
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new CheapestFilter();
    }

    /**
     * @test
     */
    public function it_returns_array_collection()
    {
        $result = $this->sut->apply(new ArrayCollection());

        $this->assertInstanceOf(ArrayCollection::class, $result);
    }

    /**
     * @test
     */
    public function it_returns_same_array_collection_if_subject_is_empty()
    {
        $collection = new ArrayCollection();
        $result = $this->sut->apply($collection);
        $this->assertSame($collection, $result);
    }

    /**
     * @test
     */
    public function it_returns_same_array_collection_if_subject_is_having_one_element()
    {
        $collection = new ArrayCollection(['something']);
        $result = $this->sut->apply($collection);

        $this->assertSame($collection, $result);
    }

    /**
     * @test
     * @dataProvider collectionDataProvider
     *
     * @param $givenCollection
     * @param $expectedItem
     */
    public function it_returns_element_with_the_least_unit_price($givenCollection, $expectedItem)
    {
        $result = $this->sut->apply($givenCollection);

        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame($expectedItem, $result->first());
    }

    ########################################################################

    /**
     * @return array[ArrayCollection, expectedOrderItem]
     */
    public function collectionDataProvider()
    {
        $orderItem1 = $this->mockOrderItemWithPrice(123);
        $orderItem2 = $this->mockOrderItemWithPrice(234);
        $orderItem3 = $this->mockOrderItemWithPrice(345);

        return [
            [
                new ArrayCollection(
                    [$orderItem1]
                ),
                $orderItem1
            ],
            [
                new ArrayCollection(
                    [$orderItem2, $orderItem1]
                ),
                $orderItem1
            ],
            [
                new ArrayCollection(
                    [$orderItem3, $orderItem2]
                ),
                $orderItem2
            ],
            [
                new ArrayCollection(
                    [$orderItem1, $orderItem3, $orderItem2]
                ),
                $orderItem1
            ]
        ];
    }

    private function mockOrderItemWithPrice($price)
    {
        $orderItem = $this->getMock(OrderItemInterface::class);

        $orderItem->method('getUnitPrice')->willReturn($price);

        return $orderItem;
    }
}
