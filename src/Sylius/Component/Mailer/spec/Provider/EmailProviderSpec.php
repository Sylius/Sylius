<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Mailer\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin \Sylius\Component\Mailer\Provider\EmailProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class EmailProviderSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, RepositoryInterface $repository)
    {
        $emails = [
            'user_confirmation' => [
                'enabled' => false,
                'subject' => 'Hello test!',
                'template' => 'SyliusMailerBundle::default.html.twig',
                'sender' => [
                    'name' => 'John Doe',
                    'address' => 'john@doe.com',
                ],
            ],
            'order_cancelled' => [
                'enabled' => false,
                'subject' => 'Hi test!',
                'template' => 'SyliusMailerBundle::default.html.twig',
                'sender' => [
                    'name' => 'Rick Doe',
                    'address' => 'john@doe.com',
                ],
            ],
        ];

        $this->beConstructedWith($factory, $repository, $emails);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Provider\EmailProvider');
    }

    function it_implements_Sylius_email_provider_interface()
    {
        $this->shouldImplement(EmailProviderInterface::class);
    }

    function it_looks_for_an_email_via_repository(RepositoryInterface $repository, EmailInterface $email)
    {
        $repository->findOneBy(['code' => 'user_confirmation'])->willReturn($email);

        $this->getEmail('user_confirmation')->shouldReturn($email);
    }

    function it_looks_for_an_email_in_configuration_when_not_found_via_repository(
        FactoryInterface $factory,
        RepositoryInterface $repository,
        EmailInterface $email
    ) {
        $repository->findOneBy(['code' => 'user_confirmation'])->shouldBeCalled()->willReturn(null);
        $factory->createNew()->shouldBeCalled()->willReturn($email);

        $email->setCode('user_confirmation')->shouldBeCalled();
        $email->setSubject('Hello test!')->shouldBeCalled();
        $email->setTemplate('SyliusMailerBundle::default.html.twig')->shouldBeCalled();
        $email->setSenderName('John Doe')->shouldBeCalled();
        $email->setSenderAddress('john@doe.com')->shouldBeCalled();
        $email->setEnabled(false)->shouldBeCalled();

        $this->getEmail('user_confirmation')->shouldReturn($email);
    }

    function it_complains_if_email_does_not_exist($repository)
    {
        $repository->findOneBy(['code' => 'foo'])->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Email with code "foo" does not exist!'))
            ->duringGetEmail('foo')
        ;
    }
}
