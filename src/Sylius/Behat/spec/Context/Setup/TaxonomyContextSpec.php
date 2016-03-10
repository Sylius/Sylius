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
    function let(RepositoryInterface $taxonRepository, FactoryInterface $taxonFactory, ObjectManager $objectManager)
    {
        $this->beConstructedWith($taxonRepository, $taxonFactory, $objectManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\TaxonomyContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_one_taxons_with_given_name(
        FactoryInterface $taxonFactory,
        RepositoryInterface $taxonRepository,
        TaxonInterface $firstTaxon
    ) {
        $taxonFactory->createNew()->willReturn($firstTaxon);

        $firstTaxon->setName('Swords')->shouldBeCalled();
        $firstTaxon->setCode('swords')->shouldBeCalled();

        $taxonRepository->add($firstTaxon)->shouldBeCalled();

        $this->storeClassifiesItsProductsAs('Swords');
    }

    function it_creates_two_taxons_with_given_name(
        FactoryInterface $taxonFactory,
        RepositoryInterface $taxonRepository,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon
    ) {
        $taxonFactory->createNew()->willReturn($firstTaxon, $secondTaxon);

        $firstTaxon->setName('Swords')->shouldBeCalled();
        $firstTaxon->setCode('swords')->shouldBeCalled();

        $secondTaxon->setName('Composite bows')->shouldBeCalled();
        $secondTaxon->setCode('composite_bows')->shouldBeCalled();

        $taxonRepository->add($firstTaxon)->shouldBeCalled();
        $taxonRepository->add($secondTaxon)->shouldBeCalled();

        $this->storeClassifiesItsProductsAs('Swords', 'Composite bows');
    }

    function it_creates_three_taxons_with_given_name(
        FactoryInterface $taxonFactory,
        RepositoryInterface $taxonRepository,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon
    ) {
        $taxonFactory->createNew()->willReturn($firstTaxon, $secondTaxon, $thirdTaxon);

        $firstTaxon->setName('Swords')->shouldBeCalled();
        $firstTaxon->setCode('swords')->shouldBeCalled();

        $secondTaxon->setName('Composite bows')->shouldBeCalled();
        $secondTaxon->setCode('composite_bows')->shouldBeCalled();

        $thirdTaxon->setName('Axes')->shouldBeCalled();
        $thirdTaxon->setCode('axes')->shouldBeCalled();

        $taxonRepository->add($firstTaxon)->shouldBeCalled();
        $taxonRepository->add($secondTaxon)->shouldBeCalled();
        $taxonRepository->add($thirdTaxon)->shouldBeCalled();

        $this->storeClassifiesItsProductsAs('Swords', 'Composite bows', 'Axes');
    }

    function it_adds_taxon_to_product(ProductInterface $product, TaxonInterface $taxon)
    {
        $product->addTaxon($taxon)->shouldBeCalled();

        $this->itBelongsTo($product, $taxon);
    }
}
