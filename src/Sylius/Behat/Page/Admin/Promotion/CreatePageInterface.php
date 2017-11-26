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
    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param string $name
     */
    public function nameIt(string $name): void;

    /**
     * @param string $ruleName
     */
    public function addRule(string $ruleName): void;

    /**
     * @param string $option
     * @param string $value
     * @param bool $multiple
     */
    public function selectRuleOption(string $option, string $value, bool $multiple = false): void;

    /**
     * @param string $option
     * @param string|string[] $value
     * @param bool $multiple
     */
    public function selectAutocompleteRuleOption(string $option, $value, $multiple = false): void;

    /**
     * @param string $option
     * @param string $value
     */
    public function fillRuleOption(string $option, string $value): void;

    /**
     * @param string $channelName
     * @param string $option
     * @param string $value
     */
    public function fillRuleOptionForChannel(string $channelName, string $option, string $value): void;

    /**
     * @param string $actionName
     */
    public function addAction(string $actionName): void;

    /**
     * @param string $option
     * @param string $value
     * @param bool $multiple
     */
    public function selectActionOption(string $option, string $value, bool $multiple = false): void;

    /**
     * @param string $option
     * @param string $value
     */
    public function fillActionOption(string $option, string $value): void;

    /**
     * @param string $channelName
     * @param string $option
     * @param string $value
     */
    public function fillActionOptionForChannel(string $channelName, string $option, string $value): void;

    /**
     * @param string $limit
     */
    public function fillUsageLimit(string $limit): void;

    public function makeExclusive(): void;

    public function checkCouponBased(): void;

    /**
     * @param string $name
     */
    public function checkChannel(string $name): void;

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setStartsAt(\DateTimeInterface $dateTime): void;

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setEndsAt(\DateTimeInterface $dateTime): void;

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAction(): string;

    /**
     * @param string $option
     * @param string|string[] $value
     * @param bool $multiple
     */
    public function selectAutoCompleteFilterOption(string $option, $value, $multiple = false): void;
}
