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

namespace spec\Sylius\Component\Mailer\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Factory\EmailFactoryInterface;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

final class EmailProviderSpec extends ObjectBehavior
{
    function let(EmailFactoryInterface $emailFactory): void
    {
        $emails = [
            'user_confirmation' => [
                'enabled' => false,
                'subject' => 'Hello test!',
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => [
                    'name' => 'John Doe',
                    'address' => 'john@doe.com',
                ],
            ],
            'order_cancelled' => [
                'enabled' => false,
                'subject' => 'Hi test!',
                'template' => '@SyliusMailer/default.html.twig',
                'sender' => [
                    'name' => 'Rick Doe',
                    'address' => 'john@doe.com',
                ],
            ],
        ];

        $this->beConstructedWith($emailFactory, $emails);
    }

    function it_implements_email_provider_interface(): void
    {
        $this->shouldImplement(EmailProviderInterface::class);
    }

    function it_looks_for_an_email_in_configuration_when_it_cannot_be_found_via_repository(
        EmailInterface $email,
        EmailFactoryInterface $emailFactory
    ): void {
        $emailFactory->createNew()->willReturn($email);

        $email->setCode('user_confirmation')->shouldBeCalled();
        $email->setSubject('Hello test!')->shouldBeCalled();
        $email->setTemplate('@SyliusMailer/default.html.twig')->shouldBeCalled();
        $email->setSenderName('John Doe')->shouldBeCalled();
        $email->setSenderAddress('john@doe.com')->shouldBeCalled();
        $email->setEnabled(false)->shouldBeCalled();

        $this->getEmail('user_confirmation')->shouldReturn($email);
    }
}
