<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @param string $ruleName
     */
    public function addRule($ruleName);

    /**
     * @param string $option
     * @param string $value
     * @param bool $multiple
     */
    public function selectRuleOption($option, $value, $multiple = false);

    /**
     * @param string $option
     * @param string|string[] $value
     * @param bool $multiple
     */
    public function selectAutocompleteRuleOption($option, $value, $multiple = false);

    /**
     * @param string $option
     * @param string $value
     */
    public function fillRuleOption($option, $value);

    /**
     * @param string $channelName
     * @param string $option
     * @param string $value
     */
    public function fillRuleOptionForChannel($channelName, $option, $value);

    /**
     * @param string $actionName
     */
    public function addAction($actionName);

    /**
     * @param string $option
     * @param string $value
     * @param bool $multiple
     */
    public function selectActionOption($option, $value, $multiple = false);

    /**
     * @param string $option
     * @param string $value
     */
    public function fillActionOption($option, $value);

    /**
     * @param string $channelName
     * @param string $option
     * @param string $value
     */
    public function fillActionOptionForChannel($channelName, $option, $value);

    /**
     * @param string $limit
     */
    public function fillUsageLimit($limit);

    public function makeExclusive();

    public function checkCouponBased();

    /**
     * @param string $name
     */
    public function checkChannel($name);

    /**
     * @param \DateTime $dateTime
     */
    public function setStartsAt(\DateTime $dateTime);

    /**
     * @param \DateTime $dateTime
     */
    public function setEndsAt(\DateTime $dateTime);

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAction();

    /**
     * @param string $option
     * @param string|string[] $value
     * @param bool $multiple
     */
    public function selectAutoCompleteFilterOption($option, $value, $multiple = false);
}
