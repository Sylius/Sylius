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
use Prophecy\Argument;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class EmailProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $emails = array(
            'user_confirmation' => array(
                'enabled'  => false,
                'subject'  => 'Hello test!',
                'template' => 'SyliusMailerBundle::default.html.twig',
                'sender'   => array(
                    'name'    => 'John Doe',
                    'address' => 'john@doe.com'
                )
            ),
            'order_cancelled' => array(
                'enabled'  => false,
                'subject'  => 'Hi test!',
                'template' => 'SyliusMailerBundle::default.html.twig',
                'sender'   => array(
                    'name'    => 'Rick Doe',
                    'address' => 'john@doe.com'
                )
            )
        );
        $this->beConstructedWith($repository, $emails);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Provider\EmailProvider');
    }

    function it_implements_Sylius_email_provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Mailer\Provider\EmailProviderInterface');
    }

    function it_looks_for_an_email_via_repository($repository, EmailInterface $email)
    {
        $repository->findOneBy(array('code' => 'user_confirmation'))->shouldBeCalled()->willReturn($email);

        $this->getEmail('user_confirmation')->shouldReturn($email);
    }

    function it_looks_for_email_in_configuration($repository, EmailInterface $email)
    {
        $repository->findOneBy(array('code' => 'user_confirmation'))->shouldBeCalled()->willReturn(null);
        $repository->createNew()->shouldBeCalled()->willReturn($email);

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
        $repository->findOneBy(array('code' => 'foo'))->shouldBeCalled()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Email with code "foo" does not exist!'))
            ->duringGetEmail('foo')
        ;
    }
}
