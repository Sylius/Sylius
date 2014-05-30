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

use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;


/**
 * Validates if the job should run in the environment and server type
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobValidator implements JobValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    private $serverValidator;

    /**
     * @var ValidatorInterface
     */
    private $environmentValidator;

    /**
     * @param ValidatorInterface $environmentValidator
     * @param ValidatorInterface $serverValidator
     */
    public function __construct(ValidatorInterface $environmentValidator, ValidatorInterface $serverValidator)
    {
        $this->environmentValidator = $environmentValidator;
        $this->serverValidator      = $serverValidator;
    }

    /**
     * Validates if the job should run in this environment and server
     *
     * @param JobInterface $job
     *
     * @return boolean
     */
    public function isValid(JobInterface $job)
    {
        $validEnvironment = $this->environmentValidator->isValid($job->getEnvironment());
        $validServer      = $this->serverValidator->isValid($job->getServerType());

        return ($validEnvironment && $validServer);
    }


}
