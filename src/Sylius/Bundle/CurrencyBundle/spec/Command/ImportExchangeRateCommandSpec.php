<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Currency\Importer\ImporterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportExchangeRateCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Command\ImportExchangeRateCommand');
    }

    function it_is_a_command()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius:currency:import');
    }

    function it_updates_a_avaivalble_exchange_rate(
        ContainerInterface $container,
        InputInterface $input,
        OutputInterface $output,
        ImporterInterface $importer,
        CurrencyInterface $currency
    ) {
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $input->validate()->shouldBeCalled();

        $output->writeln('Fetching data from external database.')->shouldBeCalled();

        $input->getArgument('importer')->shouldBeCalled()->willreturn('importer');
        $container->get('sylius.currency_importer.importer')->shouldBeCalled()->willreturn($importer);
        $importer->import()->shouldBeCalled();

        $output->writeln('Saving updated exchange rates.')->shouldBeCalled();

        $this->setContainer($container);
        $this->run($input, $output);
    }
}
