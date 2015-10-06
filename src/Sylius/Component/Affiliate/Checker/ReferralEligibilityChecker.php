<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Checker;

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\GoalInterface;
use Sylius\Component\Affiliate\Model\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sylius\Component\Affiliate\Exception\UnsupportedTypeException;

class ReferralEligibilityChecker implements ReferralEligibilityCheckerInterface
{

    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param ServiceRegistryInterface $registry
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(ServiceRegistryInterface $registry, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(GoalInterface $goal, AffiliateInterface $affiliate, $subject = null)
    {
        if (!$this->isEligibleToDates($goal)) {
            return false;
        }

        if (!$this->isEligibleToUsageLimit($goal)) {
            return false;
        }

        $eligible      = true;
        $eligibleRules = false;

        if ($goal->hasRules()) {
            /* @var RuleInterface $rule */
            foreach ($goal->getRules() as $rule) {
                try {
                    if (!$this->isEligibleToRule($subject, $goal, $rule)) {
                        return false;
                    }

                    $eligibleRules = true;
                } catch (UnsupportedTypeException $exception) {
                    if (!$eligibleRules) {
                        $eligible = false;
                    }

                    continue;
                }
            }
        }

        return $eligible;
    }

    /**
     * Checks is a goal is eligible to a subject for a given rule.
     *
     * @param mixed $subject
     * @param GoalInterface $goal
     * @param RuleInterface $rule
     * @return bool
     */
    protected function isEligibleToRule($subject, GoalInterface $goal, RuleInterface $rule)
    {
        $checker = $this->registry->get($rule->getType());

        if ($checker->supports($subject) && $checker->isEligible($subject, $rule->getConfiguration())) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the current is between date constraints.
     *
     * @param GoalInterface $goal
     *
     * @return Boolean
     */
    protected function isEligibleToDates(GoalInterface $goal)
    {
        $now = new \DateTime();

        if (null !== $startsAt = $goal->getStartsAt()) {
            if ($now < $startsAt) {
                return false;
            }
        }

        if (null !== $endsAt = $goal->getEndsAt()) {
            if ($now > $endsAt) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if goal usage limit has been reached.
     *
     * @param GoalInterface $goal
     *
     * @return Boolean
     */
    protected function isEligibleToUsageLimit(GoalInterface $goal)
    {
        if (null !== $usageLimit = $goal->getUsageLimit()) {
            if ($goal->getUsed() >= $usageLimit) {
                return false;
            }
        }

        return true;
    }
}
