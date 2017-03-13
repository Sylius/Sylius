<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminApiBundle\Model;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use FOS\OAuthServerBundle\Entity\ClientManager as FOSClientManager;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminApiBundle\Model\ClientManager;

final class ClientManagerSpec extends ObjectBehavior
{
    function let(EntityManager $em, EntityRepository $repository, $clientClass = 'Client/Class/String')
    {
        $em->getRepository($clientClass)->shouldBeCalled()->willReturn($repository);
        $this->beConstructedWith($em, $clientClass);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClientManager::class);
    }

    function it_extends_fos_oauth_server_client_manager()
    {
        $this->shouldHaveType(FOSClientManager::class);
    }

    function it_implements_fos_oauth_server_client_manager_interface()
    {
        $this->shouldImplement(ClientManagerInterface::class);
    }

    function it_finds_client_by_public_id(ClientInterface $client, $repository)
    {
        $repository->findOneBy(['randomId' => 'random_string'])->willReturn($client);

        $this->findClientByPublicId('random_string')->shouldReturn($client);
    }
}
