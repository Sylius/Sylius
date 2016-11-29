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

use RuntimeException;
use Sylius\Bundle\CoreBundle\Installer\Requirement\Requirement;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckRequirementsCommand extends AbstractInstallCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install:check-requirements')
            ->setDescription('Checks if all Sylius requirements are satisfied.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command checks system requirements.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fulfilled = true;
        $requirements = $this->get('sylius.requirements');

        $headers = ['Requirement', 'Status'];

        foreach ($requirements as $collection) {
            $rows = [];

            /** @var Requirement $requirement */
            foreach ($collection as $requirement) {
                $label = $requirement->getLabel();

                if ($requirement->isFulfilled()) {
                    $rows[] = [$label, '<info>OK!</info>'];

                    continue;
                }

                if ($requirement->isRequired()) {
                    $fulfilled = false;
                    $status = 'Z<error>ERROR!</error>';
                } else {
                    $status = '<comment>WARNING!</comment>';
                }

                $help[] = [$label, sprintf('<comment>%s</comment>', $requirement->getHelp())];
                $rows[] = [$label, $status];
            }

            if ($input->getOption('verbose') || !$fulfilled) {
                $this->renderNotFulfilledTable($output, $collection->getLabel(), $headers, $rows);
            }
        }

        if (!empty($help)) {
            $headers = ['Issue', 'Recommendation'];
            $this->renderTable($headers, $help, $output);
        }

        if (!$fulfilled) {
            throw new RuntimeException('Some system requirements are not fulfilled. Please check output messages and fix them.');
        }

        $output->writeln('<info>Success! Your system can run Sylius properly.</info>');
    }

    /**
     * @param OutputInterface $output
     * @param string $label
     * @param array $headers
     * @param array $rows
     */
    private function renderNotFulfilledTable(OutputInterface $output, $label, array $headers, array $rows)
    {
        $output->writeln(sprintf('<comment>%s</comment>', $label));
        $this->renderTable($headers, $rows, $output);
    }
}
