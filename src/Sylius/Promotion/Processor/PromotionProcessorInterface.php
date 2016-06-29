<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Promotion\Processor;

use Sylius\Promotion\Model\PromotionSubjectInterface;

/**
 * Promotion processor interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionProcessorInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     */
    public function process(PromotionSubjectInterface $subject);
}
