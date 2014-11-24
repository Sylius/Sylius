<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SearchBundle\Query;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Taxon;


/**
 * @author agounaris <agounaris@gmail.com>
 */
class TaxonQuerySpec extends ObjectBehavior
{

    function let(Taxon $taxon)
    {
        $this->beConstructedWith($taxon, array('test'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SearchBundle\Query\TaxonQuery');
    }

    public function it_has_a_taxon()
    {
        $this->getTaxon()->shouldBeAnInstanceOf('Sylius\Component\Core\Model\Taxon');
    }

    public function it_has_some_applied_filters()
    {
        $this->getAppliedFilters()->shouldReturn(array('test'));
    }

} 