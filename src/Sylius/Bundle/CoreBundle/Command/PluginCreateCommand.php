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

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Bundle\CoreBundle\Installer\Plugin\ComposerSetup;
use Sylius\Bundle\CoreBundle\Installer\Plugin\ConfigurationSetup;
use Sylius\Bundle\CoreBundle\Installer\Plugin\ExtensionSetup;
use Sylius\Bundle\CoreBundle\Command\AbstractInstallCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PluginCreateCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:plugin:create';
    protected ComposerSetup $composerSetup;

    protected function configure(): void
    {
        $this
            ->setDescription('Create a Sylius Plugin from skeleton.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows user to create basic Sylius Plugin data.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setup($input, $output, $this->getHelper('question'));

        if (!$this->askToApplyChanges($input, $output, $this->getHelper('question'))) {
            $output->writeln('The changes was aborted.');
            return 0;
        }

        $output->writeln('');

        $this->install($output);

        return 0;
    }

    private function setup(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): void
    {
        $this->composerSetup = $this->getContainer()->get('sylius.setup.plugin.composer');
        $this->composerSetup->setup($input, $output, $questionHelper);
        $this->getContainer()->get('sylius.setup.plugin.bundle')->setup($this->composerSetup, $output);
        $this->getContainer()->get('sylius.setup.plugin.configuration')->setup($this->composerSetup, $output);
        $this->getContainer()->get('sylius.setup.plugin.extension')->setup($this->composerSetup, $output);
    }

    private function install(OutputInterface $output):void
    {
        $this->getContainer()->get('sylius.setup.plugin.bundle')->install($output);
        $this->getContainer()->get('sylius.setup.plugin.configuration')->install($output);
        $this->getContainer()->get('sylius.setup.plugin.extension')->install($output);
        $this->getContainer()->get('sylius.setup.plugin.composer')->install($output);
    }

    private function askToApplyChanges(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): bool
    {
        $output->writeln('');
        $question = new ConfirmationQuestion('Are you sure to want to apply the changes? (y/N) ');

        return $questionHelper->ask($input, $output, $question);
    }
}
