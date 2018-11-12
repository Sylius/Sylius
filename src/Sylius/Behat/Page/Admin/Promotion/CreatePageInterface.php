<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code);

    public function nameIt(string $name);

    public function addRule(string $ruleName);

    public function selectRuleOption(string $option, string $value, bool $multiple = false);

    /**
     * @param string|string[] $value
     */
    public function selectAutocompleteRuleOption(string $option, $value, bool $multiple = false);

    public function fillRuleOption(string $option, string $value);

    public function fillRuleOptionForChannel(string $channelName, string $option, string $value);

    public function addAction(string $actionName);

    public function selectActionOption(string $option, string $value, bool $multiple = false);

    public function fillActionOption(string $option, string $value);

    public function fillActionOptionForChannel(string $channelName, string $option, string $value);

    public function fillUsageLimit(string $limit);

    public function makeExclusive();

    public function checkCouponBased();

    public function checkChannel(string $name);

    public function setStartsAt(\DateTimeInterface $dateTime);

    public function setEndsAt(\DateTimeInterface $dateTime);

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAction(): string;

    /**
     * @param string|string[] $value
     */
    public function selectAutoCompleteFilterOption(string $option, $value, bool $multiple = false);
}
