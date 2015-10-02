<?php

namespace Sylius\Component\Promotion\Test\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Promotion\Filter\EveryFilter;

class AllFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Subject under tests
     *
     * @var EveryFilter
     */
    private $sut;

    /**
     * @test
     */
    public function it_returns_what_it_has_been_given()
    {
        $collection = new ArrayCollection();
        $this->sut = new EveryFilter();

        $result = $this->sut->apply($collection);

        $this->assertEquals($collection, $result);
    }
}
