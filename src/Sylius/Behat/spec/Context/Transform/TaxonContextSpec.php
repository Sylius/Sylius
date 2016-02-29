<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxonContextSpec extends ObjectBehavior
{
    function let(RepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\TaxonContext');
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
}
