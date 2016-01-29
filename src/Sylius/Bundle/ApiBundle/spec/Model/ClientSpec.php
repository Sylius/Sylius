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

use FOS\OAuthServerBundle\Entity\Client;
use FOS\OAuthServerBundle\Model\ClientInterface;
use PhpSpec\ObjectBehavior;

class ClientSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\Client');
    }

    function it_extends_fos_oauth_server_client_manager()
    {
        $this->shouldHaveType(Client::class);
    }

    function it_implements_fos_oauth_server_client_manager_interface()
    {
        $this->shouldImplement(ClientInterface::class);
    }

    function it_returns_random_id_as_public_id()
    {
        $this->setRandomId('random_string');
        $this->getRandomId()->shouldReturn('random_string');
        $this->getPublicId()->shouldReturn('random_string');
    }
}
