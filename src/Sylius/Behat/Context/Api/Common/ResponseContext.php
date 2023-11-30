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

namespace Sylius\Behat\Context\Api\Common;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Webmozart\Assert\Assert;

final class ResponseContext implements Context
{
    public function __construct(private ResponseCheckerInterface $responseChecker, private ApiClientInterface $client)
    {
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            sprintf(
                'Resource could not be edited: %s',
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }

    /**
     * @Then I should be notified that it has been successfully uploaded
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyUploaded(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            sprintf(
                'Resource could not be created: %s',
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }

    /**
     * @Then I should be notified that I can no longer change payment method of this order
     */
    public function iShouldBeNotifiedThatICanNoLongerChangePaymentMethodOfThisOrder(): void
    {
        Assert::true($this->responseChecker->hasViolationWithMessage(
            $this->client->getLastResponse(),
            'You cannot change the payment method for a cancelled order.',
        ));
    }
}
