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

use PHPSpec2\ObjectBehavior;

/**
 * Cart listener spec.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartListener extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectManager               $manager
     * @param Symfony\Component\Validator\ValidatorInterface          $validator
     * @param Sylius\Bundle\CartBundle\Provider\CartProviderInterface $provider
     */
    function let($manager, $validator, $provider)
    {
        $this->beConstructedWith($manager, $validator, $provider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\CartListener');
    }
}
