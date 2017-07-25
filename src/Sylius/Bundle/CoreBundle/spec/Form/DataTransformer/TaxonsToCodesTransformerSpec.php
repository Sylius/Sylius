<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\TaxonsToCodesTransformer;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonsToCodesTransformerSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonsToCodesTransformer::class);
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_array_of_taxons_codes_to_taxons_collection(
        TaxonRepositoryInterface $taxonRepository,
        TaxonInterface $bows,
        TaxonInterface $swords
    ) {
        $taxonRepository->findBy(['code' => ['bows', 'swords']])->willReturn([$bows, $swords]);

        $this->transform(['bows', 'swords'])->shouldIterateAs([$bows, $swords]);
    }

    function it_transforms_only_existing_taxons(
        TaxonRepositoryInterface $taxonRepository,
        TaxonInterface $bows
    ) {
        $taxonRepository->findBy(['code' => ['bows', 'swords']])->willReturn([$bows]);

        $this->transform(['bows', 'swords'])->shouldIterateAs([$bows]);
    }

    function it_transforms_empty_array_into_empty_collection()
    {
        $this->transform([])->shouldIterateAs([]);
    }

    function it_throws_exception_if_value_to_transform_is_not_array()
    {
        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', 'array'))
            ->during('transform', ['badObject'])
        ;
    }

    function it_reverse_transforms_into_array_of_taxons_codes(
        TaxonInterface $axes,
        TaxonInterface $shields
    ) {
        $axes->getCode()->willReturn('axes');
        $shields->getCode()->willReturn('shields');

        $this
            ->reverseTransform(new ArrayCollection([$axes->getWrappedObject(), $shields->getWrappedObject()]))
            ->shouldIterateAs(['axes', 'shields'])
        ;
    }

    function it_throws_exception_if_reverse_transformed_object_is_not_collection()
    {
        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', Collection::class))
            ->during('reverseTransform', ['badObject'])
        ;
    }

    function it_returns_empty_array_if_passed_collection_is_empty()
    {
        $this->reverseTransform(new ArrayCollection())->shouldReturn([]);
    }
}
