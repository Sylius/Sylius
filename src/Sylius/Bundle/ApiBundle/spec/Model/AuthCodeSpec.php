<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ApiBundle\Model;

use FOS\OAuthServerBundle\Entity\AuthCode;
use PhpSpec\ObjectBehavior;

class AuthCodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\AuthCode');
    }

    function it_is_a_auth_code()
    {
        $this->shouldHaveType(AuthCode::class);
    }
}
