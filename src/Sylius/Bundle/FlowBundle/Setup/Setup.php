<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Setup;

use Sylius\Bundle\FlowBundle\Setup\Builder\SetupBuilderInterface;
use Sylius\Bundle\FlowBundle\Setup\Step\StepInterface;

abstract class Setup implements SetupInterface
{
    protected $steps;
    protected $alias;

    public function validate($currentStepIndex)
    {
        $nbOfSteps = $this->countSteps();

        if ($nbOfSteps < 1) {
            throw new \LogicException('Add steps and build the setup to valdiate it');
        }

        for ($i = 0; $i < $currentStepIndex; $i++) {
            if (!$this->steps[$i]->isSkipped() && !$this->steps[$i]->isCompleted()) {
                return false;
            }
        }

        return true;
    }

    public function countSteps()
    {
        return count($this->steps);
    }

    public function getStep($index)
    {
        if (isset($this->steps[$index])) {
            return $this->steps[$index];
        }

        throw new \InvalidArgumentException('Wrong step index supplied for this setup');
    }

    public function setStep($index, StepInterface $step)
    {
        if (isset($this->steps[$index])) {
            throw new \InvalidArgumentException('Step with given index already exists');
        }

        $this->steps[$index] = $step;
    }

    public function hasStep($index)
    {
        return isset($this->steps[$index]);
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }
}
