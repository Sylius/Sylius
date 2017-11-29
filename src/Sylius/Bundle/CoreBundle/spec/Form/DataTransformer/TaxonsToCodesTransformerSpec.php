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
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class TaxonsToCodesTransformerSpec extends ObjectBehavior
{
    public function let(TaxonRepositoryInterface $taxonRepository): void
    {
        $this->beConstructedWith($taxonRepository);
    }

    public function it_implements_data_transformer_interface(): void
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    public function it_transforms_array_of_taxons_codes_to_taxons_collection(
        TaxonRepositoryInterface $taxonRepository,
        TaxonInterface $bows,
        TaxonInterface $swords
    ): void {
        $taxonRepository->findBy(['code' => ['bows', 'swords']])->willReturn([$bows, $swords]);

        $this->transform(['bows', 'swords'])->shouldIterateAs([$bows, $swords]);
    }

    public function it_transforms_only_existing_taxons(
        TaxonRepositoryInterface $taxonRepository,
        TaxonInterface $bows
    ): void {
        $taxonRepository->findBy(['code' => ['bows', 'swords']])->willReturn([$bows]);

        $this->transform(['bows', 'swords'])->shouldIterateAs([$bows]);
    }

    public function it_transforms_empty_array_into_empty_collection(): void
    {
        $this->transform([])->shouldIterateAs([]);
    }

    public function it_throws_exception_if_value_to_transform_is_not_array(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('transform', ['badObject'])
        ;
    }

    public function it_reverse_transforms_into_array_of_taxons_codes(
        TaxonInterface $axes,
        TaxonInterface $shields
    ): void {
        $axes->getCode()->willReturn('axes');
        $shields->getCode()->willReturn('shields');

        $this
            ->reverseTransform(new ArrayCollection([$axes->getWrappedObject(), $shields->getWrappedObject()]))
            ->shouldIterateAs(['axes', 'shields'])
        ;
    }

    public function it_throws_exception_if_reverse_transformed_object_is_not_collection(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('reverseTransform', ['badObject'])
        ;
    }

    public function it_returns_empty_array_if_passed_collection_is_empty(): void
    {
        $this->reverseTransform(new ArrayCollection())->shouldReturn([]);
    }
}
