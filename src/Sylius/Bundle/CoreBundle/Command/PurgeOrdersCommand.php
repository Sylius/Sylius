<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Ka-Yue Yeung <kayuey@gmail.com>
 */
class PurgeOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:order:purge')
            ->setDescription('Purge expired pending orders')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orderPendingDuration = $this->getContainer()->getParameter('sylius.order.pending.duration');
        $expiresAt = null;

        if ($input->isInteractive()) {
            $dialog = $this->getHelperSet()->get('dialog');
            $dialog->askAndValidate($output, sprintf('<question>Order pending duration (%s)?</question>', $orderPendingDuration), function ($response) use ($orderPendingDuration, &$expiresAt) {
                if (null !== $response) {
                    $orderPendingDuration = $response;
                }
                $expiresAt = new \DateTime(sprintf('-%s', $orderPendingDuration));

                return $orderPendingDuration;
            });
        } else {
            $expiresAt = new \DateTime(sprintf('-%s', $orderPendingDuration));
        }

        $output->writeln('Purging expired pending orders...');

        $ordersPurger = $this->getContainer()->get('sylius.order.purger');
        $ordersPurger->setExpiresAt($expiresAt);
        $ordersPurger->purge();

        $output->writeln('Expired pending orders purged.');
    }
}
