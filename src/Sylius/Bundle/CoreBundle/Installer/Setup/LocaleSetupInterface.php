<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Setup;

use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface LocaleSetupInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return LocaleInterface
     */
    public function setup(InputInterface $input, OutputInterface $output);
}
