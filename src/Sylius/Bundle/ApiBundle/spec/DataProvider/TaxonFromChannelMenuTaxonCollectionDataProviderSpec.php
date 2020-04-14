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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TaxonFromChannelMenuTaxonCollectionDataProviderSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository): void
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_supports_only_get_from_menu_taxon_operation_on_taxon_entity(): void
    {
        $this->supports(TaxonInterface::class, 'get_from_menu_taxon')->shouldReturn(true);
        $this->supports(ProductInterface::class, 'get_from_menu_taxon')->shouldReturn(false);
    }

    function it_throws_an_exception_if_context_has_not_channel(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [TaxonInterface::class, 'get_from_menu_taxon', []])
        ;
    }

    function it_provides_taxon_from_channel_menu_taxon(
        TaxonRepositoryInterface $taxonRepository,
        TaxonInterface $menuTaxon,
        TaxonInterface $firstChildTaxon,
        TaxonInterface $secondChildTaxon,
        ChannelInterface $channel
    ): void {
        $channel->getMenuTaxon()->willReturn($menuTaxon);
        $taxonRepository->findChildrenByChannelMenuTaxon($menuTaxon)->willReturn([$firstChildTaxon, $secondChildTaxon]);

        $this
            ->getCollection(
                TaxonInterface::class,
                'get_from_menu_taxon',
                [ContextKeys::CHANNEL => $channel])
            ->shouldReturn([$firstChildTaxon, $secondChildTaxon]);
    }
}
