<?php

namespace spec\Sylius\Component\ImportExport\Provider;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CurrentDateProviderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('America/New_York');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Provider\CurrentDateProvider');
    }

    function it_implements_date_provider_inteface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Provider\CurrentDateProviderInterface');
    }

    function it_provides_current_date()
    {
        $this->getCurrentDate()->shouldBeLike(new \DateTime());
    }
}