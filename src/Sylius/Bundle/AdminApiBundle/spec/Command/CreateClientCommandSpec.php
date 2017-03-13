<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminApiBundle\Command;

use FOS\OAuthServerBundle\Model\ClientManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AdminApiBundle\Model\Client;
use Sylius\Bundle\AdminApiBundle\Command\CreateClientCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class CreateClientCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CreateClientCommand::class);
    }

    public function it_is_a_container_aware_command()
    {
        $this->shouldHaveType(ContainerAwareCommand::class);
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius:oauth-server:create-client');
    }

    public function it_creates_a_client(
        ContainerInterface $container,
        InputInterface $input,
        OutputInterface $output,
        ClientManager $clientManager,
        Client $client
    ) {
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $input->validate()->shouldBeCalled();
        $input->hasArgument('command')->willReturn(false);

        $container->get('fos_oauth_server.client_manager.default')->willReturn($clientManager);
        $clientManager->createClient()->willReturn($client);

        $input->getOption('redirect-uri')->willReturn(['redirect-uri']);
        $input->getOption('grant-type')->willReturn(['grant-type']);

        $client->setRedirectUris(['redirect-uri'])->shouldBeCalled();
        $client->setAllowedGrantTypes(['grant-type'])->shouldBeCalled();

        $clientManager->updateClient($client)->shouldBeCalled();

        $client->getPublicId()->shouldBeCalled();
        $client->getSecret()->shouldBeCalled();

        $output->writeln(Argument::type('string'))->shouldBeCalled();

        $this->setContainer($container);
        $this->run($input, $output);
    }
}
