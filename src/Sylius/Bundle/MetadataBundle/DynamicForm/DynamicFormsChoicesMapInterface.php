<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\DynamicForm;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface DynamicFormsChoicesMapInterface
{
    /**
     * @param string $group
     * @param string $dataClass
     * @param string $formName
     */
    public function addForm($group, $dataClass, $formName);

    /**
     * @param string $group
     *
     * @return string[]
     */
    public function getFormsNamesByGroup($group);

    /**
     * @param string $group
     * @param string $dataClass
     *
     * @return string|null
     *
     */
    public function getFormNameByGroupAndDataClass($group, $dataClass);

    /**
     * @param string $group
     * @param string $formName
     *
     * @return string|null
     */
    public function getDataClassByGroupAndFormName($group, $formName);
}
