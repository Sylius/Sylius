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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    private CountryFactoryInterface $countryFactory;

    public function __construct(CountryFactoryInterface $countryFactory)
    {
        parent::__construct('sylius:test');

        $this->countryFactory = $countryFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->countryFactory::getEntityClass());

        return self::SUCCESS;
    }
}
