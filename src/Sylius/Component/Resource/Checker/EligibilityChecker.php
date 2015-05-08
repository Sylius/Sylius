<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Checker;

use Sylius\Component\Resource\Exception\UnsupportedTypeException;
use Sylius\Component\Resource\Model\RuleAwareInterface;
use Sylius\Component\Resource\Model\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Checks if rules are eligible.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class EligibilityChecker implements EligibilityCheckerInterface
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
    public function __construct(ServiceRegistryInterface $registry, EventDispatcherInterface $dispatcher = null)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible($subject, RuleAwareInterface $object)
    {
        if (!$this->supports($subject, $object)) {
            return false;
        }

        if (!$this->isEligibleToDates($object)) {
            return false;
        }

        $eligible = true;
        $eligibleRules = false;
        if ($object->hasRules()) {
            foreach ($object->getRules() as $rule) {
                try {
                    if (!$this->isEligibleToRule($subject, $object, $rule)) {
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
     * Checks is eligible to a subject for a given rule.
     *
     * @param object             $subject
     * @param RuleAwareInterface $object
     * @param RuleInterface      $rule
     *
     * @return bool
     */
    protected function isEligibleToRule($subject, RuleAwareInterface $object, RuleInterface $rule)
    {
        $checker = $this->registry->get($rule->getType());

        if ($checker->isEligible($subject, $rule->getConfiguration())) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the current is between time limits.
     *
     * @param object $object
     *
     * @return bool
     */
    protected function isEligibleToDates($object)
    {
        $now = new \DateTime();

        if (null !== $startsAt = $object->getStartsAt()) {
            return $now > $startsAt;
        }

        if (null !== $endsAt = $object->getEndsAt()) {
            return $now < $endsAt;
        }

        return true;
    }

    /**
     * @param object $subject
     * @param object $object
     *
     * @return bool
     */
    abstract protected function supports($subject, $object);
}
