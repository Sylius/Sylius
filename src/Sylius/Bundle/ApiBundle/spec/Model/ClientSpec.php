<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use PhpSpec\ObjectBehavior;

class ClientSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\Client');
    }

    public function it_extends_fos_oauth_server_client_manager()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\Client');
    }

    public function it_implements_fos_oauth_server_client_manager_interface()
    {
        $this->shouldImplement('FOS\OAuthServerBundle\Model\ClientInterface');
    }

    public function it_returns_random_id_as_public_id()
    {
        $this->setRandomId('random_string');
        $this->getRandomId()->shouldReturn('random_string');
        $this->getPublicId()->shouldReturn('random_string');
    }
}
