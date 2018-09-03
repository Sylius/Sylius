<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminApiBundle\Command;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Sylius\Bundle\AdminApiBundle\Model\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated Fetching dependencies directly from container is not recommended from Symfony 3.4. Extending `ContainerAwareCommand` will be removed in 2.0
 */
final class CreateClientCommand extends ContainerAwareCommand
{
    /**
     * @var ClientManagerInterface
     */
    private $clientManager;

    public function __construct(?string $name = null, ClientManagerInterface $clientManager = null)
    {
        parent::__construct($name);

        $this->clientManager = $clientManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:oauth-server:create-client')
            ->setDescription('Creates a new client')
            ->addOption(
                'redirect-uri',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets redirect uri for client.'
            )
            ->addOption(
                'grant-type',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets allowed grant type for client.'
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info>command creates a new client.
<info>php %command.full_name% [--redirect-uri=...] [--grant-type=...] name</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if (null === $this->clientManager) {
            @trigger_error('Fetching services directly from the container is deprecated since Sylius 1.2 and will be removed in 2.0.', E_USER_DEPRECATED);
            $this->clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        }

        /** @var Client $client */
        $client = $this->clientManager->createClient();
        $client->setRedirectUris($input->getOption('redirect-uri'));
        $client->setAllowedGrantTypes($input->getOption('grant-type'));
        $this->clientManager->updateClient($client);

        $output->writeln(
            sprintf(
                'A new client with public id <info>%s</info>, secret <info>%s</info> has been added',
                $client->getPublicId(),
                $client->getSecret()
            )
        );
    }
}
