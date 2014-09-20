<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Validator;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;


/**
 * Scheduler validator, it validates if a job has to run now and if global scheduler settings are enabled
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SchedulerValidator implements SchedulerValidatorInterface
{
    /**
     * @var \Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var ValidatorInterface
     */
    private $scheduleValidator;

    /**
     * @param SettingsManagerInterface $settingsManager
     * @param ValidatorInterface       $scheduleValidator
     */
    public function __construct(SettingsManagerInterface $settingsManager, ValidatorInterface $scheduleValidator)
    {
        $this->settingsManager   = $settingsManager;
        $this->scheduleValidator = $scheduleValidator;
    }

    /**
     * Returns if global  is enabled
     *
     * @return boolean
     */
    public function isSchedulerEnabled()
    {
        return $this->settingsManager->loadSettings('job_scheduler')->get('enabled');
    }

    /**
     * Given a pattern it returns if it should run now
     *
     * @param $pattern
     *
     * @return boolean
     */
    public function isScheduleValid($pattern)
    {
        return $this->scheduleValidator->isValid($pattern);
    }
} 