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

namespace Sylius\Behat\Context\Ui\Admin\Helper;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Component\Core\Formatter\StringInflector;
use Webmozart\Assert\Assert;

trait ValidationTrait
{
    /**
     * @When I specify a too long :field
     */
    public function iSpecifyATooLong(string $field): void
    {
        $this->resolveCurrentPage()->specifyField(ucwords($field), str_repeat('a', 256));
    }

    /**
     * @Then I should be notified that :field is too long
     * * @Then I should be notified that :field should be no longer than :maxLength characters
     */
    public function iShouldBeNotifiedThatFieldValueIsTooLong(string $field, int $maxLength = 255): void
    {
        Assert::contains(
            $this->resolveCurrentPage()->getValidationMessage(StringInflector::nameToLowercaseCode($field)),
            sprintf('must not be longer than %d characters.', $maxLength),
        );
    }

    /**
     * @return SymfonyPageInterface&SpecifiesItsField
     */
    abstract protected function resolveCurrentPage(): SymfonyPageInterface;
}
