<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Command;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Purger\PurgerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
        $this->shouldHaveType(ContainerAwareCommand::class);
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
