<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Definition;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ArrayToDefinitionConverter implements ArrayToDefinitionConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert($code, array $configuration)
    {
        $grid = Grid::fromCodeAndDriverConfiguration($code, $configuration['driver']['name'], $configuration['driver']['options']);

        if (array_key_exists('sorting', $configuration)) {
            $grid->setSorting($configuration['sorting']);
        }

        foreach ($configuration['fields'] as $name => $fieldConfiguration) {
            $field = Field::fromNameAndType($name, $fieldConfiguration['type']);

            if (array_key_exists('path', $fieldConfiguration)) {
                $field->setPath($fieldConfiguration['path']);
            }
            if (array_key_exists('label', $fieldConfiguration)) {
                $field->setLabel($fieldConfiguration['label']);
            }
            if (array_key_exists('enabled', $fieldConfiguration)) {
                $field->setEnabled($fieldConfiguration['enabled']);
            }
            if (array_key_exists('options', $fieldConfiguration)) {
                $field->setOptions($fieldConfiguration['options']);
            }

            $grid->addField($field);
        }

        foreach ($configuration['filters'] as $name => $filterConfiguration) {
            $filter = Filter::fromNameAndType($name, $filterConfiguration['type']);

            if (array_key_exists('label', $filterConfiguration)) {
                $filter->setLabel($filterConfiguration['label']);
            }
            if (array_key_exists('options', $filterConfiguration)) {
                $filter->setOptions($filterConfiguration['options']);
            }

            $grid->addFilter($filter);
        }

        foreach ($configuration['actions'] as $groupName => $actions) {
            $actionGroup = ActionGroup::named($groupName);

            foreach ($actions as $name => $actionConfiguration) {
                $action = Action::fromNameAndType($name, $actionConfiguration['type']);

                if (array_key_exists('label', $actionConfiguration)) {
                    $action->setLabel($actionConfiguration['label']);
                }
                if (array_key_exists('options', $actionConfiguration)) {
                    $action->setOptions($actionConfiguration['options']);
                }

                $actionGroup->addAction($action);
            }

            $grid->addActionGroup($actionGroup);
        }

        return $grid;
    }
}
