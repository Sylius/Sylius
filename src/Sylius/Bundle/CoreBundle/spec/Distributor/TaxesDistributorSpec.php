<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Distributor;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Distributor\TaxesDistributorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxesDistributorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Distributor\TaxesDistributor');
    }

    function it_implements_order_unit_taxes_distributor_interface()
    {
        $this->shouldImplement(TaxesDistributorInterface::class);
    }

    function it_distributes_simple_taxes()
    {
        $this->distribute(4, 1000)->shouldReturn(array(250, 250, 250, 250));;
        $this->distribute(4, -1000)->shouldReturn(array(-250, -250, -250, -250));;
    }

    function it_distributes_taxes_that_cannot_be_split_equally()
    {
        $this->distribute(3, 1000)->shouldReturn(array(334, 333, 333));;
        $this->distribute(3, -1000)->shouldReturn(array(-334, -333, -333));;
    }

    function it_throws_exception_if_tax_items_number_is_not_integer_or_below_1()
    {
        $this->shouldThrow(new \InvalidArgumentException('Tax items number must be an integer, bigger than 0.'))->during('distribute', array('test', 1000));
        $this->shouldThrow(new \InvalidArgumentException('Tax items number must be an integer, bigger than 0.'))->during('distribute', array(0, 1000));
    }
}
