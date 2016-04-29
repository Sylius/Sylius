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
 * @author Foo Pang <foo.pang@gmail.com>
 */
class ReleaseOrdersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:order:release')
            ->setDescription('Release expired pending orders')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inventoryHoldingDuration = $this->getContainer()->getParameter('sylius.inventory.holding.duration');
        $expiresAt = null;

        if ($input->isInteractive()) {
            $dialog = $this->getHelperSet()->get('dialog');
            $dialog->askAndValidate($output, sprintf('<question>Inventory holding duration (%s)?</question>', $inventoryHoldingDuration), function ($response) use ($inventoryHoldingDuration, &$expiresAt) {
                if (null !== $response) {
                    $inventoryHoldingDuration = $response;
                }
                $expiresAt = new \DateTime(sprintf('-%s', $inventoryHoldingDuration));

                return $inventoryHoldingDuration;
            });
        } else {
            $expiresAt = new \DateTime(sprintf('-%s', $inventoryHoldingDuration));
        }

        $output->writeln('Release expired pending orders...');

        $ordersReleaser = $this->getContainer()->get('sylius.order.releaser');
        $ordersReleaser->release($expiresAt);

        $output->writeln('Expired pending orders released.');
    }
}
