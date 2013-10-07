<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Command;

use Sylius\Bundle\SubscriptionBundle\Event\SubscriptionEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Command to process subscriptions
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ProcessSubscriptionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:subscription:process')
            ->setDescription('Process Subscriptions')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Processing Subscriptions...');

        $counter = new \stdClass();
        $counter->total = 0;
        $counter->errors = 0;

        $this->attachListeners($output, $counter);

        $subscriptionProcessor = $this->getContainer()->get('sylius.subscription.processor');
        $subscriptionProcessor->process();

        $tag = (0 === $counter->errors) ? 'info' : 'error';
        $output->writeln(sprintf('<%s>%s Subscription(s) processed (%s errors).</%s>', $tag, $counter->total, $counter->errors, $tag));
    }

    protected function attachListeners(OutputInterface $output, $counter)
    {
        $dispatcher = $this->getContainer()->get('event_dispatcher');

        $dispatcher->addListener(SubscriptionEvents::SUBSCRIPTION_PROCESS_INITIALIZE, function (GenericEvent $event) use ($output, $counter) {
            $output->write(sprintf('<comment>Processing Subscription %s...</comment>', $event->getSubject()->getId()));
            $counter->total++;
        });

        $dispatcher->addListener(SubscriptionEvents::SUBSCRIPTION_PROCESS_COMPLETED, function (GenericEvent $event) use ($output) {
            $output->writeln('<info>OK</info>');
        });

        $dispatcher->addListener(SubscriptionEvents::SUBSCRIPTION_PROCESS_ERROR, function (GenericEvent $event) use ($output, $counter) {
            $output->writeln('<error>ERROR (%s)</error>', $event['exception']->getMessage());
            $counter->errors++;
        });
    }
}