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

namespace Sylius\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class EmailContext implements Context
{
    /** @var EmailCheckerInterface */
    private $emailChecker;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(EmailCheckerInterface $emailChecker, TranslatorInterface $translator)
    {
        $this->emailChecker = $emailChecker;
        $this->translator = $translator;
    }

    /**
     * @Then an email with reset token should be sent to :recipient
     * @Then an email with reset token should be sent to :recipient in :localeCode locale
     */
    public function anEmailWithResetTokenShouldBeSentTo(string $recipient, string $localeCode = 'en_US'): void
    {
        $this->assertEmailContainsMessageTo(
            $this->translator->trans('sylius.email.password_reset.reset_your_password', [], null, $localeCode),
            $recipient
        );
    }

    private function assertEmailContainsMessageTo(string $message, string $recipient): void
    {
        Assert::true($this->emailChecker->hasMessageTo($message, $recipient));
    }
}
