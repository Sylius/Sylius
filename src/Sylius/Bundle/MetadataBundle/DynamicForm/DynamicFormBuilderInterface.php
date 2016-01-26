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

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface DynamicFormBuilderInterface
{
    /**
     * Adds dynamic form field to given builder's form, sets up events
     * and transformers to handle it properly.
     *
     * @param FormBuilderInterface $builder
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function buildDynamicForm(FormBuilderInterface $builder, $name, $type, array $options = []);
}
