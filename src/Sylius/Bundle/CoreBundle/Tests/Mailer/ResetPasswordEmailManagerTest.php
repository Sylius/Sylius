<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\Mailer;

use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Core\Model\AdminUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ResetPasswordEmailManagerTest extends KernelTestCase
{
    private const RECIPIENT_EMAIL = 'sylius@example.com';

    private ResetPasswordEmailManagerInterface $resetPasswordEmailManager;

    private TranslatorInterface $translator;

    private AdminUser $adminUser;

    protected function setUp(): void
    {
        $this->resetPasswordEmailManager = self::getContainer()->get(ResetPasswordEmailManagerInterface::class);

        $this->translator = self::getContainer()->get('translator');

        $this->adminUser = new AdminUser();
        $this->adminUser->setEmail(self::RECIPIENT_EMAIL);
    }

    /** @test */
    public function it_sends_admin_reset_password_email(): void
    {
        $this->resetPasswordEmailManager->sendAdminResetPasswordEmail($this->adminUser, 'en_US');

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', self::RECIPIENT_EMAIL);
        self::assertEmailHtmlBodyContains(
            $email,
            $this->translator->trans(
                id: 'sylius.email.admin_password_reset.to_reset_your_password_token',
                locale: 'en_US',
            ),
        );
    }
}
