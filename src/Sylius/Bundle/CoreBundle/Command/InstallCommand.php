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

use Sylius\Bundle\CoreBundle\Command\Helper\DirectoryChecker;
use Sylius\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;

final class InstallCommand extends Command
{
    /**
     * @var DirectoryChecker
     */
    private $directoryChecker;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var CommandExecutor
     */
    private $commandExecutor;

    /** @var array */
    private $commands = [
        [
            'command' => 'check-requirements',
            'message' => 'Checking system requirements.',
        ],
        [
            'command' => 'database',
            'message' => 'Setting up the database.',
        ],
        [
            'command' => 'setup',
            'message' => 'Shop configuration.',
        ],
        [
            'command' => 'assets',
            'message' => 'Installing assets.',
        ],
    ];

    public function __construct(DirectoryChecker $directoryChecker, string $cacheDir)
    {
        $this->directoryChecker = $directoryChecker;
        $this->cacheDir = $cacheDir;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:install')
            ->setDescription('Installs Sylius in your preferred environment.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs Sylius.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->commandExecutor = new CommandExecutor($input, $output, $this->getApplication());
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Installing Sylius...</info>');
        $outputStyle->writeln($this->getSyliusLogo());

        $this->directoryChecker->ensureDirectoryExistsAndIsWritable($this->cacheDir, $output, $this->getName());

        $errored = false;
        foreach ($this->commands as $step => $command) {
            try {
                $outputStyle->newLine();
                $outputStyle->section(sprintf(
                    'Step %d of %d. <info>%s</info>',
                    $step + 1,
                    count($this->commands),
                    $command['message']
                ));
                $this->commandExecutor->runCommand('sylius:install:' . $command['command'], [], $output);
            } catch (RuntimeException $exception) {
                $errored = true;
            }
        }

        $outputStyle->newLine(2);
        $outputStyle->success($this->getProperFinalMessage($errored));
        $outputStyle->writeln('You can now open your store at the following path under the website root: /');
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
