<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PHPSpec2\ObjectBehavior;

/**
 * Request fetcher spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class RequestFetcher extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\HttpFoundation\ParameterBag $attributeBag
     */
    function let($request, $attributeBag)
    {
        $request->attributes = $attributeBag;

        $this->beConstructedWith($request);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\RequestFetcher');
    }
}
