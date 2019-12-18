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

namespace spec\Sylius\Bundle\AdminApiBundle\Form\ChoiceList\Loader;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

final class LazyCustomerLoaderSpec extends ObjectBehavior
{
    function let(CustomerRepositoryInterface $customerRepository): void
    {
        $this->beConstructedWith($customerRepository);
    }

    function it_is_choice_loader(): void
    {
        $this->shouldImplement(ChoiceLoaderInterface::class);
    }

    function it_loads_customers_by_email(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $firstCustomer,
        CustomerInterface $secondCustomer
    ): void {
        $customerRepository
            ->findBy(['email' => ['first@example.com', 'second@example.com']])
            ->willReturn([$firstCustomer, $secondCustomer])
        ;

        $this
            ->loadChoicesForValues(['first@example.com', 'second@example.com'])
            ->shouldReturn([$firstCustomer, $secondCustomer])
        ;
    }

    function it_does_not_load_any_choices_available(): void
    {
        $this->loadValuesForChoices([])->shouldReturn([]);
    }

    function it_provides_empty_array_choice_list(): void
    {
        $this->loadChoiceList()->shouldBeAnInstanceOf(ArrayChoiceList::class);
    }
}
