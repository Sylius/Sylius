<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Action;

<<<<<<< HEAD:src/Sylius/Bundle/PromotionsBundle/Action/PromotionActionInterface.php
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
=======
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
>>>>>>> Creating Promotion component:src/Sylius/Component/Promotion/Action/PromotionActionInterface.php

/**
 * Executes promotion action on given subject.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionActionInterface
{
    /**
     * Applies the promotion to its subject.
     *
     * @param \Sylius\Component\Promotion\Model\PromotionSubjectInterface $subject
     * @param array $configuration
     * @return mixed
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion);

    /**
     * Returns the form name related to this action.
     *
     * @return string
     */
    public function getConfigurationFormType();
}
