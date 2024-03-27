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

namespace Sylius\Behat\Context\Api\Admin\Helper;

use Sylius\Component\Core\Formatter\StringInflector;
use Webmozart\Assert\Assert;

trait ValidationTrait
{
    /**
     * @When I specify a too long :field
     */
    public function iSpecifyATooLong(string $field): void
    {
        $this->client->addRequestData($field, str_repeat('a', $this->getMaxCodeLength() + 1));
    }

    /**
     * @Then I should be notified that :field is too long
     * @Then I should be notified that :field should be no longer than :maxLength characters
     */
    public function iShouldBeNotifiedThatFieldIsTooLong(string $field, int $maxLength = 255): void
    {
        Assert::regex(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('/%s\: .+ must not be longer than %d characters./', StringInflector::nameToCamelCase($field), $maxLength),
        );
    }

    private function getMaxCodeLength(): int
    {
        return 255;
    }
}
