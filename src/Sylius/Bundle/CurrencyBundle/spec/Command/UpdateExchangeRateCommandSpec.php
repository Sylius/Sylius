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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Currency\Importer\ImporterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UpdateExchangeRateCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Command\UpdateExchangeRateCommand');
    }

    function it_is_a_command()
    {
        $this->shouldHaveType(ContainerAwareCommand::class);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius:currency:update');
    }

    function it_updates_a_available_exchange_rate(
        ContainerInterface $container,
        InputInterface $input,
        OutputInterface $output,
        CurrencyProviderInterface $currencyProvider,
        ImporterInterface $importer,
        CurrencyInterface $currency
    ) {
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $input->validate()->shouldBeCalled();
        $input->hasArgument('command')->willReturn(false);

        $output->writeln('Fetching data from external database.')->shouldBeCalled();

        $input->hasOption('all')->shouldBeCalled()->willreturn(false);
        $container->get('sylius.currency_provider')->shouldBeCalled()->willreturn($currencyProvider);
        $currencyProvider->getAvailableCurrencies()->shouldBeCalled()->willreturn([$currency]);

        $input->getArgument('importer')->shouldBeCalled()->willreturn('importer');
        $container->get('sylius.currency_importer.importer')->shouldBeCalled()->willreturn($importer);
        $importer->import([$currency])->shouldBeCalled();

        $output->writeln('Saving updated exchange rates.')->shouldBeCalled();

        $this->setContainer($container);
        $this->run($input, $output);
    }

    function it_updates_all_exchange_rate(
        ContainerInterface $container,
        InputInterface $input,
        OutputInterface $output,
        EntityRepository $repository,
        ImporterInterface $importer,
        CurrencyInterface $currency
    ) {
        $input->bind(Argument::any())->shouldBeCalled();
        $input->isInteractive()->shouldBeCalled();
        $input->validate()->shouldBeCalled();
        $input->hasArgument('command')->willReturn(false);

        $output->writeln('Fetching data from external database.')->shouldBeCalled();

        $input->hasOption('all')->shouldBeCalled()->willreturn(true);
        $container->get('sylius.repository.currency')->shouldBeCalled()->willreturn($repository);
        $repository->findAll()->shouldBeCalled()->willreturn([$currency]);

        $input->getArgument('importer')->shouldBeCalled()->willreturn('importer');
        $container->get('sylius.currency_importer.importer')->shouldBeCalled()->willreturn($importer);
        $importer->import([$currency])->shouldBeCalled();

        $output->writeln('Saving updated exchange rates.')->shouldBeCalled();

        $this->setContainer($container);
        $this->run($input, $output);
    }
}
