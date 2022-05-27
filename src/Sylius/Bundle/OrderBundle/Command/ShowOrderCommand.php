<?php

namespace Sylius\Bundle\OrderBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowOrderCommand extends Command
{
    protected static $defaultName = 'sylius:order:show';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }

}
