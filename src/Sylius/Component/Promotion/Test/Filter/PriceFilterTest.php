<?php

namespace Sylius\Component\Promotion\Test\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Expr\Comparison;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Filter\PriceFilter;

class PriceFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Subject under tests
     *
     * @var PriceFilter
     */
    private $sut;

    /**
     * @dataProvider collectionDataProvider
     * @test
     *
     * @param $configuration
     * @param $collection
     * @param $expectedResult
     */
    public function it_returns_values_according_to_configuration($configuration, $collection, $expectedResult)
    {
        $this->sut = new PriceFilter($configuration);

        $result = $this->sut->apply($collection);

        $this->assertInstanceOf(ArrayCollection::class, $result);
        $this->assertSameSize($expectedResult, $result);

        foreach ($result as $itemInResult)
        {
            $this->assertContains($itemInResult, $expectedResult);
        }
    }

    ########################################################################

    /**
     * @return array[$configuration, $collection, $expectedResult]
     */
    public function collectionDataProvider()
    {
        $orderItem1 = $this->mockOrderItemWithPrice(123);
        $orderItem2 = $this->mockOrderItemWithPrice(234);
        $orderItem3 = $this->mockOrderItemWithPrice(345);

        $config1 = $this->getConfigWithOptions(123, Comparison::GTE);
        $config2 = $this->getConfigWithOptions(234, Comparison::GTE);
        $config3 = $this->getConfigWithOptions(345, Comparison::GTE);
        $config4 = $this->getConfigWithOptions(123, Comparison::GT);
        $config5 = $this->getConfigWithOptions(345, Comparison::LT);
        $config6 = $this->getConfigWithOptions(234, Comparison::NEQ);

        $threeItemCollection = new ArrayCollection([$orderItem1, $orderItem2, $orderItem3]);

        return [
            [
                $config1, $threeItemCollection,
                new ArrayCollection(
                    [$orderItem1, $orderItem2, $orderItem3]
                )
            ],
            [
                $config2, $threeItemCollection,
                new ArrayCollection(
                    [$orderItem2, $orderItem3]
                )
            ],
            [
                $config3, $threeItemCollection,
                new ArrayCollection(
                    [$orderItem3]
                )
            ],
            [
                $config4, $threeItemCollection,
                new ArrayCollection(
                    [$orderItem2, $orderItem3]
                )
            ],
            [
                $config5, $threeItemCollection,
                new ArrayCollection(
                    [$orderItem1, $orderItem2]
                )
            ],
            [
                $config6, $threeItemCollection,
                new ArrayCollection(
                    [$orderItem1, $orderItem3]
                )
            ]
        ];
    }

    private function mockOrderItemWithPrice($price)
    {
        $orderItem = $this->getMock(OrderItemInterface::class);

        $orderItem->method('getUnitPrice')->willReturn($price);

        return $orderItem;
    }

    private function getConfigWithOptions($value, $comparison)
    {
        return [
            PriceFilter::OPTION_VALUE => $value,
            PriceFilter::OPTION_COMPARISON => $comparison,
        ];
    }
}
