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

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ChannelDefaultTaxZoneSetupInterface
{
    public function setup(
        ZoneInterface $zone,
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
    ): void;
}
