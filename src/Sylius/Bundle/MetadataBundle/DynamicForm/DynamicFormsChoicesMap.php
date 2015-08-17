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
class DynamicFormsChoicesMap implements DynamicFormsChoicesMapInterface
{
    /**
     * Maps data class to form name.
     *
     * @var string[]
     */
    protected $forms;

    /**
     * {@inheritdoc}
     */
    public function addForm($group, $dataClass, $formName)
    {
        $this->forms[$group][$dataClass] = $formName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormsNamesByGroup($group)
    {
        return isset($this->forms[$group]) ? $this->forms[$group] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormNameByGroupAndDataClass($group, $dataClass)
    {
        foreach ($this->getFormsNamesByGroup($group) as $currentDataClass => $formName) {
            if ($currentDataClass === $dataClass) {
                return $formName;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataClassByGroupAndFormName($group, $formName)
    {
        foreach ($this->getFormsNamesByGroup($group) as $dataClass => $currentFormName) {
            if ($currentFormName === $formName) {
                return $dataClass;
            }
        }

        return null;
    }
}
