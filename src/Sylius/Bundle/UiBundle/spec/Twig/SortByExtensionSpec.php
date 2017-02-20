<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UiBundle\Twig;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Twig\SortByExtension;
use Sylius\Bundle\UiBundle\spec\Fixtures\SampleInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class SortByExtensionSpec extends ObjectBehavior
{
    function it_extends_twig_extensions()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_sorts_in_ascending_order_by_default(
        SampleInterface $firstSample,
        SampleInterface $secondSample,
        SampleInterface $thirdSample
    ) {
        $firstSample->getInt()->willReturn(3);
        $secondSample->getInt()->willReturn(5);
        $thirdSample->getInt()->willReturn(1);

        $arrayBeforeSorting = [
            $firstSample,
            $secondSample,
            $thirdSample,
        ];

        $this->sortBy($arrayBeforeSorting, 'int')->shouldReturn(
            [
                $thirdSample,
                $firstSample,
                $secondSample,
            ]
        );
    }

    function it_sorts_an_array_of_objects_by_various_properties(
        SampleInterface $firstSample,
        SampleInterface $secondSample,
        SampleInterface $thirdSample
    ) {
        $firstSample->getInt()->willReturn(3);
        $secondSample->getInt()->willReturn(5);
        $thirdSample->getInt()->willReturn(1);

        $firstSample->getString()->willReturn('true');
        $secondSample->getString()->willReturn('123');
        $thirdSample->getString()->willReturn('Alohomora');

        $firstSample->getBizarrelyNamedProperty()->willReturn('banana');
        $secondSample->getBizarrelyNamedProperty()->willReturn(123);
        $thirdSample->getBizarrelyNamedProperty()->willReturn(null);

        $arrayBeforeSorting = [
            $firstSample,
            $secondSample,
            $thirdSample,
        ];

        $this->sortBy($arrayBeforeSorting, 'int')->shouldReturn(
            [
                $thirdSample,
                $firstSample,
                $secondSample,
            ]
        );

        $this->sortBy($arrayBeforeSorting, 'string')->shouldReturn(
            [
                $secondSample,
                $thirdSample,
                $firstSample,
            ]
        );

        $this->sortBy($arrayBeforeSorting, 'bizarrelyNamedProperty')->shouldReturn(
            [
                $thirdSample,
                $secondSample,
                $firstSample,
            ]
        );
    }

    function it_sorts_an_array_of_objects_in_descending_order_by_a_property(
        SampleInterface $firstSample,
        SampleInterface $secondSample,
        SampleInterface $thirdSample
    ) {
        $firstSample->getInt()->willReturn(3);
        $secondSample->getInt()->willReturn(5);
        $thirdSample->getInt()->willReturn(1);

        $arrayBeforeSorting = [
            $firstSample,
            $secondSample,
            $thirdSample,
        ];

        $this->sortBy($arrayBeforeSorting, 'int', 'DESC')->shouldReturn(
            [
                $secondSample,
                $firstSample,
                $thirdSample,
            ]
        );
    }

    function it_sorts_an_array_of_objects_by_a_nested_property(
        SampleInterface $firstSample,
        SampleInterface $secondSample,
        SampleInterface $thirdSample,
        SampleInterface $firstInnerSample,
        SampleInterface $secondInnerSample,
        SampleInterface $thirdInnerSample
    ) {
        $firstInnerSample->getString()->willReturn('m');
        $secondInnerSample->getString()->willReturn('Z');
        $thirdInnerSample->getString()->willReturn('A');

        $firstSample->getInnerSample()->willReturn($firstInnerSample);
        $secondSample->getInnerSample()->willReturn($secondInnerSample);
        $thirdSample->getInnerSample()->willReturn($thirdInnerSample);

        $arrayBeforeSorting = [
            $firstSample,
            $secondSample,
            $thirdSample,
        ];

        $this->sortBy($arrayBeforeSorting, 'innerSample.string', 'ASC')->shouldReturn(
            [
                $thirdSample,
                $firstSample,
                $secondSample,
            ]
        );
    }

    function it_throws_an_exception_if_the_property_is_not_found_on_objects(
        SampleInterface $firstSample,
        SampleInterface $secondSample,
        SampleInterface $thirdSample
    ) {
        $arrayBeforeSorting = [
            $firstSample,
            $secondSample,
            $thirdSample,
        ];

        $this
            ->shouldThrow(NoSuchPropertyException::class)
            ->during('sortBy', [$arrayBeforeSorting, 'nonExistingProperty'])
        ;
    }

    function it_return_input_array_if_there_is_only_one_object_inside(SampleInterface $sample)
    {
        $this->sortBy([$sample], 'property')->shouldReturn([$sample]);
    }

    function it_does_nothing_if_array_is_empty()
    {
        $this->sortBy([], 'property')->shouldReturn([]);
    }

    function it_does_nothing_if_collection_is_empty(Collection $collection)
    {
        $collection->toArray()->willReturn([]);

        $this->sortBy($collection, 'property')->shouldReturn([]);
    }
}
