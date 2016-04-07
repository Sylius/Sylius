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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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
     * @param string $value
     */
    public function fillRuleOption($option, $value);
}
