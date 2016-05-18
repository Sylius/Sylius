<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Generator;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface InstructionInterface
{
    /**
     * @return int
     */
    public function getAmount();

    /**
     * @return int
     */
    public function getCodeLength();

    /**
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * @return int
     */
    public function getUsageLimit();
}
