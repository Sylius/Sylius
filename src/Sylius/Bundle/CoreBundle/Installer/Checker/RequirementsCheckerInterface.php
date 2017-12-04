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

namespace Sylius\Bundle\CoreBundle\Installer\Checker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface RequirementsCheckerInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function check(InputInterface $input, OutputInterface $output): bool;
}
