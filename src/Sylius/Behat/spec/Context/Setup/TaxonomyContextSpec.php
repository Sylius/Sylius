<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxonomyContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $taxonRepository,
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonomyFactory,
        TaxonFactoryInterface $taxonFactory,
        ObjectManager $objectManager
    ) {
        $this->beConstructedWith(
            $taxonRepository,
            $taxonomyRepository,
            $taxonomyFactory,
            $taxonFactory,
            $objectManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\TaxonomyContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_returns_taxon_by_name($taxonRepository, TaxonInterface $taxon)
    {
        $taxonRepository->findOneBy(['name' => 'Books'])->willReturn($taxon);

        $this->getTaxonByName('Books')->shouldReturn($taxon);
    }

    function it_throws_exception_if_taxon_with_given_name_does_not_exist($taxonRepository)
    {
        $taxonRepository->findOneBy(['name' => 'Books'])->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Taxon with name "Books" does not exist.'))
            ->during('getTaxonByName', ['Books'])
        ;
    }

    function it_creates_taxons_with_given_name(
        $taxonomyRepository,
        $taxonomyFactory,
        $taxonFactory,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon,
        TaxonomyInterface $taxonomy
    ) {
        $taxonomyFactory->createNew()->willReturn($taxonomy);
        $taxonomy->setCode('category')->shouldBeCalled();
        $taxonomy->setName('Category')->shouldBeCalled();

        $taxonFactory->createNew()->willReturn($firstTaxon, $secondTaxon, $thirdTaxon);

        $firstTaxon->setName('Swords')->shouldBeCalled();
        $firstTaxon->setCode('swords')->shouldBeCalled();

        $secondTaxon->setName('Composite bows')->shouldBeCalled();
        $secondTaxon->setCode('composite_bows')->shouldBeCalled();

        $thirdTaxon->setName('Axes')->shouldBeCalled();
        $thirdTaxon->setCode('axes')->shouldBeCalled();

        $taxonomy->addTaxon($firstTaxon)->shouldBeCalled();
        $taxonomy->addTaxon($secondTaxon)->shouldBeCalled();
        $taxonomy->addTaxon($thirdTaxon)->shouldBeCalled();

        $taxonomyRepository->add($taxonomy)->shouldBeCalled();

        $this->storeClassifiesItsProductsAs('Swords', 'Composite bows', 'Axes');
    }

    function it_adds_taxon_to_product($taxonRepository, ProductInterface $product, TaxonInterface $taxon)
    {
        $taxonRepository->findOneBy(['name' => 'Books'])->willReturn($taxon);

        $product->addTaxon($taxon)->shouldBeCalled();

        $this->itBelongsTo($product, 'Books');
    }
}
