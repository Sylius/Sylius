<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Installer\Provider;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface DatabaseSetupCommandsProviderInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return array
     */
    public function getCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper);
}
