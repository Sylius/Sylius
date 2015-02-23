<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\Common;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractTargetResolver implements TargeResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(ContainerBuilder $container, array $interfaces)
    {
        $getTargetResolverService = $this->getTargetResolverService();
        if (!$container->hasDefinition($getTargetResolverService)) {
            throw new \RuntimeException('Cannot find Doctrine RTEL');
        }

        $resolveTargetEntityListener = $container->findDefinition($getTargetResolverService);

        foreach ($interfaces as $interface => $model) {
            $resolveTargetEntityListener
                ->addMethodCall($this->getMethodName(), array(
                    $this->getInterface($container, $interface),
                    $this->getClass($container, $model),
                    array()
                ))
            ;
        }

        $tagName = $this->getTagName();
        if (!$resolveTargetEntityListener->hasTag($tagName)) {
            $resolveTargetEntityListener->addTag($tagName, array('event' => 'loadClassMetadata'));
        }
    }

    /**
     * Return the tag name
     *
     * @return string
     */
    abstract protected function getTagName();

    /**
     * Return the name of the method which is called on the listener
     *
     * @return string
     */
    abstract protected function getMethodName();

    /**
     * Return the doctrine resolve target listener
     *
     * @return string
     */
    abstract protected function getTargetResolverService();

    /**
     * @param ContainerBuilder $container
     * @param string           $key
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getInterface(ContainerBuilder $container, $key)
    {
        if ($container->hasParameter($key)) {
            return $container->getParameter($key);
        }

        if (interface_exists($key)) {
            return $key;
        }

        throw new \InvalidArgumentException(
            sprintf('The interface %s does not exist.', $key)
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $key
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function getClass(ContainerBuilder $container, $key)
    {
        if ($container->hasParameter($key)) {
            return $container->getParameter($key);
        }

        if (class_exists($key)) {
            return $key;
        }

        throw new \InvalidArgumentException(
            sprintf('The class %s does not exist.', $key)
        );
    }
}