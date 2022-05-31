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
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Component\Filesystem\Filesystem;

final class EmailSpoolContext implements Context
{
    private string $spoolDirectory;

    public function __construct(
        EmailCheckerInterface $emailChecker,
        private Filesystem $filesystem
    ) {
        $this->spoolDirectory = $emailChecker->getSpoolDirectory();
    }

    /**
     * @BeforeScenario @email
     */
    public function purgeSpooledMessages()
    {
        $this->filesystem->remove($this->spoolDirectory);
    }
}
