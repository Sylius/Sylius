<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class DynamicFormsChoicesMapCompilerPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        if (!$container->hasDefinition('sylius.metadata.dynamic_forms_choices_map')) {
            return;
        }

        $definition = $container->getDefinition('sylius.metadata.dynamic_forms_choices_map');

        $taggedServices = $container->findTaggedServiceIds('sylius.metadata.dynamic_form_choice');

        foreach ($taggedServices as $id => $tags) {
            $formDefinition = $container->getDefinition($id);

            $dataClass = $this->getFormDataClass($formDefinition);
            $formName = $this->getFormName($formDefinition);

            $definition->addMethodCall('addForm', [
                $tags[0]['group'],
                $dataClass,
                $formName,
                isset($tags[0]['label']) ? $tags[0]['label'] : $formName
            ]);
        }
    }

    /**
     * @param Definition $formDefinition
     *
     * @return string Form data class
     */
    private function getFormDataClass(Definition $formDefinition)
    {
        $tags = $formDefinition->getTag('sylius.metadata.dynamic_form_choice');
        $class = isset($tags[0]['class']) ? $tags[0]['class'] : null;

        if (null === $class) {
            throw new \InvalidArgumentException(sprintf(
                'Definition "%s" tagged by "%s" should define class attribute in tag definition',
                $formDefinition->getClass(),
                'sylius.metadata.dynamic_form'
            ));
        }

        return $class;
    }

    /**
     * @param Definition $formDefinition
     *
     * @return string Form name
     */
    private function getFormName(Definition $formDefinition)
    {
        $tags = $formDefinition->getTag('form.type');
        $formName = isset($tags[0]['alias']) ? $tags[0]['alias'] : null;

        if (null === $formName) {
            throw new \InvalidArgumentException(sprintf(
                'Definition "%s" tagged by "%s" should also be tagged by "%s" with attribute "%s"',
                $formDefinition->getClass(),
                'sylius.metadata.dynamic_form',
                'form.type',
                'alias'
            ));
        }

        return $formName;
    }
}
