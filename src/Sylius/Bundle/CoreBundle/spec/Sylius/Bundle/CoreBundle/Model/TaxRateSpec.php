<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRateSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\TaxRate');
    }

    function it_should_implement_Sylius_core_tax_rate_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\TaxRateInterface');
    }

    function it_should_extend_Sylius_tax_rate_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Model\TaxRate');
    }

    function it_should_not_have_any_zone_defined_by_default()
    {
        $this->getZone()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface $zone
     */
    function it_should_allow_defining_zone($zone)
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }
}
