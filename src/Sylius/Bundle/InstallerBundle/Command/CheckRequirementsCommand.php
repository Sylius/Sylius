<?php

namespace Sylius\Bundle\InstallerBundle\Command;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckRequirementsCommand extends AbstractInstallCommand
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

        $headers = array('Requirement', 'Status');

        foreach ($requirements as $collection) {
            $rows = array();

            foreach ($collection as $requirement) {
                $label = $requirement->getLabel();

                if ($requirement->isFulfilled()) {
                    $status = '<info>OK!</info>';
                } else {
                    $comment = sprintf('<comment>%s</comment>', $requirement->getHelp());

                    if ($requirement->isRequired()) {
                        $fulfilled = false;
                        $status = ' <error>ERROR!</error>';
                    } else {
                        $status = '<comment>WARNING!</comment>';
                    }

                    $help[] = array($label, $comment);
                }

                $rows[] = array($label, $status);
            }

            if ($input->getOption('verbose') || !$fulfilled) {
                $output->writeln(sprintf('<comment>%s</comment>', $collection->getLabel()));
                $this->renderTable($headers, $rows, $output);
            }
        }

        if (!empty($help)) {
            $headers = array('Issue', 'Recommendation');
            $this->renderTable($headers, $help, $output);
        }

        if (!$fulfilled) {
            throw new RuntimeException('Some system requirements are not fulfilled. Please check output messages and fix them.');
        } else {
            $output->writeln('<info>Success! Your system can run Sylius properly.</info>');
        }
    }
}
