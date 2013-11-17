<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $manager, ValidatorInterface $validator, CartProviderInterface $provider)
    {
        $this->beConstructedWith($manager, $validator, $provider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\CartListener');
    }
}
