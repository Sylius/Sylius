<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use FOS\OAuthServerBundle\Model\ClientInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientManagerSpec extends ObjectBehavior
{
    function let(EntityManager $em, EntityRepository $repository, $clientClass = 'Client/Class/String')
    {
        $em->getRepository($clientClass)->shouldBeCalled()->willReturn($repository);
        $this->beConstructedWith($em, $clientClass);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\ClientManager');
    }

    function it_extends_fos_oauth_server_client_manager()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\ClientManager');
    }

    function it_implements_fos_oauth_server_client_manager_interface()
    {
        $this->shouldImplement('FOS\OAuthServerBundle\Model\ClientManagerInterface');
    }

    function it_finds_client_by_public_id(ClientInterface $client, $repository)
    {
        $repository->findOneBy(array('randomId'  => 'random_string'))->shouldBeCalled()->willReturn($client);

        $this->findClientByPublicId('random_string')->shouldReturn($client);
    }
}
