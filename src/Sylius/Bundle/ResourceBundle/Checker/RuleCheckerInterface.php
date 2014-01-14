<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Checker;

use Sylius\Bundle\ResourceBundle\Model\SubjectInterface;

/**
 * Rule checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleCheckerInterface
{
    /**
     * @param SubjectInterface $subject
     * @param array            $configuration
     *
     * @return Boolean
     */
    public function isEligible(SubjectInterface $subject, array $configuration);

    /**
     * @return string
     */
    public function getConfigurationFormType();
}
