<?php

namespace spec\Sylius\Bundle\ApiBundle\Command;

use FOS\OAuthServerBundle\Model\ClientManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Model\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateClientCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Command\CreateClientCommand');
    }

    public function it_is_a_container_aware_command()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand');
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius:oauth-server:create-client');
    }

    public function it_create_client(
        ContainerInterface $container,
        InputInterface $input,
        OutputInterface $output,
        ClientManager $clientManager,
        Client $client
    ) {
        $container->get('fos_oauth_server.client_manager.default')->willReturn($clientManager);
        $clientManager->createClient()->willReturn($client);

        $input->getOption('redirect-uri')->willReturn(array('redirect-uri'));
        $input->getOption('grant-type')->willReturn(array('grant-type'));

        $client->setRedirectUris(array('redirect-uri'))->shouldBeCalled();
        $client->setAllowedGrantTypes(array('grant-type'))->shouldBeCalled();

        $clientManager->updateClient($client)->shouldBeCalled();

        $client->getPublicId()->shouldBeCalled();
        $client->getSecret()->shouldBeCalled();

        $output->writeln(Argument::type('string'))->shouldBeCalled();

        $this->setContainer($container);
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $input->validate()->shouldBeCalled();
        $this->run($input, $output);
    }
}
