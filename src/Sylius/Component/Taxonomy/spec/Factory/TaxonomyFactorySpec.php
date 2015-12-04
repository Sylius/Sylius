<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxonomy\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Component\Translation\Factory\TranslatableFactoryInterface;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class TaxonomyFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $taxonFactory, FactoryInterface $factory, LocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($factory, $localeProvider, $taxonFactory, 'Sylius\Component\Taxonomy\Model\Taxonomy');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxonomy\Factory\TaxonomyFactory');
    }

    function it_implements_factory_interface()
    {
        $this->shouldImplement(FactoryInterface::class);
    }
}
