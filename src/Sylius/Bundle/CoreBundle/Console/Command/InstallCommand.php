<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;

final class InstallCommand extends AbstractInstallCommand
{
    protected static $defaultName = 'sylius:install';

    /** @var array<int, array<string, string>> */
    private array $commands = [
        [
            'command' => 'sylius:install:check-requirements',
            'message' => 'Checking system requirements.',
        ],
        [
            'command' => 'sylius:install:database',
            'message' => 'Setting up the database.',
        ],
        [
            'command' => 'sylius:install:setup',
            'message' => 'Shop configuration.',
        ],
        [
            'command' => 'sylius:install:jwt-setup',
            'message' => 'Configuring JWT token.',
        ],
        [
            'command' => 'sylius:install:assets',
            'message' => 'Installing assets.',
        ],
        [
            'command' => 'cache:clear',
            'message' => 'Clearing cache.',
        ],
    ];

    protected function configure(): void
    {
        $this
            ->setDescription('Installs Sylius in your preferred environment.')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command installs Sylius.
EOT
            )
            ->addOption('fixture-suite', 's', InputOption::VALUE_OPTIONAL, 'Load specified fixture suite during install', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $suite = $input->getOption('fixture-suite');

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Installing Sylius...</info>');
        $outputStyle->writeln($this->getSyliusLogo());

        $this->ensureDirectoryExistsAndIsWritable((string) $this->getContainer()->getParameter('kernel.cache_dir'), $output);

        $errored = false;
        foreach ($this->commands as $step => $command) {
            try {
                $outputStyle->newLine();
                $outputStyle->section(sprintf(
                    'Step %d of %d. <info>%s</info>',
                    $step + 1,
                    count($this->commands),
                    $command['message'],
                ));

                $parameters = [];
                if ('sylius:install:database' === $command['command'] && null !== $suite) {
                    $parameters['--fixture-suite'] = $suite;
                }

                $this->commandExecutor->runCommand($command['command'], $parameters, $output);
            } catch (RuntimeException) {
                $errored = true;
            }
        }

        $outputStyle->newLine(2);
        $outputStyle->success($this->getProperFinalMessage($errored));
        $outputStyle->writeln('You can now open your store at the following path under the website root: /');

        return $errored ? 1 : 0;
    }

    private function getProperFinalMessage(bool $errored): string
    {
        if ($errored) {
            return 'Sylius has been installed, but some error occurred.';
        }

        return 'Sylius has been successfully installed.';
    }

    private function getSyliusLogo(): string
    {
        return '
           <info>,</info>
         <info>,;:,</info>
       <info>`;;;.:`</info>
      <info>`::;`  :`</info>
       <info>:::`   `</info>          .\'++:           \'\'.   \'.
       <info>`:::</info>             :+\',;+\'          :+;  `+.
        <info>::::</info>            +\'   :\'          `+;
        <info>`:::,</info>           \'+`     ++    :+.`+; `++. ;+\'    \'\'  ,++++.
         <info>,:::`</info>          `++\'.   .+:  `+\' `+;  .+,  ;+    +\'  +;  \'\'
          <info>::::`</info>           ,+++.  \'+` :+. `+;  `+,  ;+    +\'  \'+.
   <info>,.     .::::</info>             .++` `+: +\'  `+;  `+,  ;+    +\'  `;++;
<info>`;;.:::`   :::::</info>             :+.  \'+,+.  `+;  `+,  ;+   `+\'     .++
 <info>.;;;;;;::`.::::,</info>       +\'` `++   `++\'   `+;  `+:  :+. `++\'  \'.  ;+
  <info>,;;;;;;;;;:::::</info>       .+++++`    ;+,    ++;  ++, `\'+++,\'+\' :++++,
   <info>,;;;;;;;;;:::</info>`                  ;\'
    <info>:;;;;;;;;;:,</info>                :.:+,
     <info>;;;;;;;;;:</info>                 ;++,'
        ;
    }
}

class_alias(InstallCommand::class, '\Sylius\Bundle\CoreBundle\Command\InstallCommand');
