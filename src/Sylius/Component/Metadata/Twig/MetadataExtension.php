<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Twig;

use Sylius\Component\Metadata\Accessor\MetadataAccessorInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class MetadataExtension extends \Twig_Extension
{
    /**
     * @var MetadataAccessorInterface
     */
    private $metadataAccessor;

    /**
     * @var MetadataRendererInterface
     */
    private $metadataRenderer;

    /**
     * @param MetadataAccessorInterface $metadataAccessor
     * @param MetadataRendererInterface $metadataRenderer
     */
    public function __construct(MetadataAccessorInterface $metadataAccessor, MetadataRendererInterface $metadataRenderer)
    {
        $this->metadataAccessor = $metadataAccessor;
        $this->metadataRenderer = $metadataRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_metadata_get', [$this, 'getProperty']),
            new \Twig_SimpleFunction('sylius_metadata_render', [$this, 'renderProperty']),
        ];
    }

    /**
     * @param MetadataSubjectInterface $metadataSubject
     * @param string|null $propertyPath
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function getProperty(MetadataSubjectInterface $metadataSubject, $propertyPath = null, $defaultValue = null)
    {
        return $this->metadataAccessor->getProperty($metadataSubject, $propertyPath) ?: $defaultValue;
    }

    /**
     * @param MetadataSubjectInterface $metadataSubject
     * @param string|null $propertyPath
     * @param array $options
     *
     * @return string
     */
    public function renderProperty(MetadataSubjectInterface $metadataSubject, $propertyPath = null, array $options = [])
    {
        $metadataProperty = $this->metadataAccessor->getProperty($metadataSubject, $propertyPath);

        if (null === $metadataProperty) {
            return null;
        }

        return $this->metadataRenderer->render($metadataProperty, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_metadata';
    }
}
