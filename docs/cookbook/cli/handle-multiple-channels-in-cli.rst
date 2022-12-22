Handle multiple channels in CLI
===============================

When we use directly or indirectly any service depending on a channel context, we are not able to define which channel we want to use, when there are more than two.
Your primary goal should be to avoid such cases, but if you have to, there is a way to do it.

What is our goal?
-----------------

To accomplish this task, we have to create a custom channel context available only from the CLI context. But this concrete channel context cannot only return a channel,
but also has to allow to pass a channel code as an argument (which we will get from the console command). This way, we can use the channel context in our services.

1. Custom Channel Context
-------------------------

First, we need to create a custom channel context. Let's remind our requirements:

    * available only from the CLI
    * there must be a way to pass in a channel code

Our suggested solution is the following:

.. code-block:: php

    // src/Channel/Context/CliBasedChannelContext.php
    <?php

    declare(strict_types=1);

    namespace App\Channel\Context;

    use Sylius\Component\Channel\Context\ChannelNotFoundException;
    use Sylius\Component\Channel\Model\ChannelInterface;
    use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

    final class CliBasedChannelContext implements CliBasedChannelContextInterface
    {
        private ?string $channelCode = null;

        public function __construct(
            private ChannelRepositoryInterface $channelRepository,
        ) {
        }

        public function setChannelCode(?string $channelCode): void
        {
            $this->channelCode = $channelCode;
        }

        public function getChannelCode(): ?string
        {
            return $this->channelCode;
        }

        public function getChannel(): ChannelInterface
        {
            if ('cli' !== PHP_SAPI || null === $this->channelCode) {
                throw new ChannelNotFoundException();
            }

            $channel = $this->channelRepository->findOneByCode($this->channelCode);

            if (null === $channel) {
                throw new ChannelNotFoundException();
            }

            return $channel;
        }
    }

.. code-block:: php

    // src/Channel/Context/CliBasedChannelContextInterface.php;
    <?php

    declare(strict_types=1);

    namespace App\Channel\Context;

    use Sylius\Component\Channel\Context\ChannelContextInterface;

    interface CliBasedChannelContextInterface extends ChannelContextInterface
    {
        public function setChannelCode(?string $channelCode): void;

        public function getChannelCode(): ?string;
    }

Now, we have to configure our custom channel context as a service:

.. code-block:: yaml

    # config/services.yaml
    services:
        App\Channel\Context\CliBasedChannelContextInterface:
            class: App\Channel\Context\CliBasedChannelContext
            arguments:
                - '@sylius.repository.channel'
            tags:
                - { name: 'sylius.context.channel', priority: -256 }

2. Usage of the new custom channel context in a console command
---------------------------------------------------------------

Now, we can use our custom channel context in our console command. For proof of our concept we will create a DummyCommand,
which will take a channel code as an option and will dispatch a dummy event. This event is handled by an event subscriber using
the channel context to get the channel and print its name.

You command might look like this:

.. code-block:: yaml

    // src/Console/Command/DummyCommand.php
    <?php

    declare(strict_types=1);

    namespace App\Console\Command;

    use App\Channel\Context\CliBasedChannelContextInterface;
    use App\Console\Command\Event\DummyEvent; // it is just a dummy event, nothing special there
    use Symfony\Component\Console\Attribute\AsCommand;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\EventDispatcher\EventDispatcherInterface;

    #[AsCommand('app:dummy', description: 'Dummy command')]
    class DummyCommand extends Command
    {
        public function __construct (
            private CliBasedChannelContextInterface $cliBasedChannelContext,
            private EventDispatcherInterface $dispatcher,
        ) {
            parent::__construct();
        }

        protected function configure(): void
        {
            $this
                ->addOption('channel', 'c', InputOption::VALUE_OPTIONAL, 'Channel code')
            ;
        }

        protected function execute(InputInterface $input, OutputInterface $output): int
        {
            if (null !== $channelCode = $input->getOption('channel')) {
                $this->cliBasedChannelContext->setChannelCode($channelCode);
            }

            // it isn't important what happens inside the event subscriber.
            // It just gets channel from the channel context service
            // and prints its name
            $this->dispatcher->dispatch(new DummyEvent());

            return Command::SUCCESS;
        }
    }

The output of the example is following:

.. code-block:: bash

    /app $ bin/console app:dummy -c MAGIC_WEB
    Hi! I am Dummy Event Subscriber. I am using Channel Context.
    Your channel name is: Magic Web Channel
    /app $ bin/console app:dummy -c FASHION_WEB
    Hi! I am Dummy Event Subscriber. I am using Channel Context.
    Your channel name is: Fashion Web Store
