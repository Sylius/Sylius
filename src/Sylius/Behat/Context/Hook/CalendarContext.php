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

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;

final class CalendarContext implements Context
{
    private string $projectDirectory;

    public function __construct(string $projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @AfterScenario
     */
    public function deleteTemporaryDate(): void
    {
        if (file_exists($this->projectDirectory . '/var/temporaryDate.txt')) {
            unlink($this->projectDirectory . '/var/temporaryDate.txt');
        }
    }
}
