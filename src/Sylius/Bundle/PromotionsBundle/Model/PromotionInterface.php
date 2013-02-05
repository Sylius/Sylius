<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Model;

use DateTime;

/**
 * Promotion model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionInterface
{
    public function getId();
    public function getName();
    public function setName($name);
    public function getDescription();
    public function setDescription($desciption);
    public function getCode();
    public function setCode($code);
    public function getUsageLimit();
    public function setUsageLimit($usageLimit);
    public function getUsed();
    public function incrementUsed();
    public function getStartsAt();
    public function setStartsAt(DateTime $startsAt);
    public function getEndsAt();
    public function setEndsAt(DateTime $endsAt);
    public function getRules();
    public function hasRule(RuleInterface $rule);
    public function addRule(RuleInterface $rule);
    public function removeRule(RuleInterface $rule);
    public function getActions();
    public function hasAction(ActionInterface $action);
    public function addAction(ActionInterface $action);
    public function removeAction(ActionInterface $action);
    public function getUpdatedAt();
    public function getCreatedAt();
}
