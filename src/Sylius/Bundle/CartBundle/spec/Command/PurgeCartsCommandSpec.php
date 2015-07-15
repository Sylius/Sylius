<?php

namespace spec\Sylius\Bundle\CartBundle\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Purger\PurgerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PurgeCartsCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Command\PurgeCartsCommand');
    }

    public function it_is_a_command()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand');
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius:cart:purge');
    }

    public function it_imports_the_vet(
        ContainerInterface $container,
        InputInterface $input,
        OutputInterface $output,
        PurgerInterface $purger
    ) {
        $output->writeln('Purging expired carts...')->shouldBeCalled();

        $container->get('sylius.cart.purger')->shouldBeCalled()->willReturn($purger);
        $purger->purge()->shouldBeCalled();

        $output->writeln('Expired carts purged.')->shouldBeCalled();

        $this->setContainer($container);
        $this->run($input, $output);
    }
}
