<?php

namespace spec\Sylius\Bundle\ThemeBundle\Hydrator;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Hydrator\ThemeHydrator;
use Zend\Hydrator\HydratorInterface;

/**
 * @mixin ThemeHydrator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeHydratorSpec extends ObjectBehavior
{
    function let(HydratorInterface $decoratedHydrator)
    {
        $this->beConstructedWith($decoratedHydrator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Hydrator\ThemeHydrator');
    }

    function it_implements_zend_hydrator_interface()
    {
        $this->shouldImplement(HydratorInterface::class);
    }

    function it_just_proxies_extracting_if_there_is_no_parents_key_in_extracted_data(HydratorInterface $decoratedHydrator)
    {
        $decoratedHydrator->extract('object')->willReturn(['data' => 'data']);

        $this->extract('object')->shouldReturn(['data' => 'data']);
    }

    function it_proxies_extracting_and_converts_parents_to_array_if_needed(
        HydratorInterface $decoratedHydrator,
        Collection $collection
    ) {
        $decoratedHydrator->extract('object')->willReturn(['data' => 'data', 'parents' => $collection]);

        $collection->toArray()->willReturn(['parent object']);

        $this->extract('object')->shouldReturn(['data' => 'data', 'parents' => ['parent object']]);
    }

    function it_just_proxies_hydrating_if_there_is_no_parents_key_in_hydrated_data(HydratorInterface $decoratedHydrator)
    {
        $decoratedHydrator->hydrate(['data' => 'data'], 'object')->willReturn('hydrated object');

        $this->hydrate(['data' => 'data'], 'object')->shouldReturn('hydrated object');
    }

    function it_proxies_hydrating_and_converts_parents_to_collection_if_needed(HydratorInterface $decoratedHydrator)
    {
        $decoratedHydrator->hydrate(Argument::that(function (array $data) {
            if (!isset($data['data']) || $data['data'] !== 'data') {
                return false;
            }

            return isset($data['parents'])
                && $data['parents'] instanceof Collection
                && $data['parents']->toArray() === ['parent object']
            ;
        }), 'object')->willReturn('hydrated object');

        $this->hydrate(['data' => 'data', 'parents' => ['parent object']], 'object')->shouldReturn('hydrated object');
    }
}
