<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class EmailSpoolContext implements Context
{
    /**
     * @var string
     */
    private $spoolDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param EmailCheckerInterface $emailChecker
     * @param Filesystem $filesystem
     */
    public function __construct(EmailCheckerInterface $emailChecker, Filesystem $filesystem)
    {
        $this->spoolDirectory = $emailChecker->getSpoolDirectory();
        $this->filesystem = $filesystem;
    }

    /**
     * @BeforeScenario @email
     */
    public function purgeSpooledMessages()
    {
        $this->filesystem->remove($this->spoolDirectory);
    }
}
