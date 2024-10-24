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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Webmozart\Assert\Assert;

final class TranslationContext implements Context
{
    public function __construct(
        private readonly ApiClientInterface $client,
        private readonly ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @Then I should be notified that the locale is not available
     */
    public function iShouldBeNotifiedThatLocaleIsNotAvailable(): void
    {
        Assert::contains($this->responseChecker->getError($this->client->getLastResponse()), 'Please choose one of the available locales');
    }
}
