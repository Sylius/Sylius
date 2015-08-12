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
class MetadataExtension extends \Twig_Extension
{
    /**
     * @var MetadataAccessorInterface
     */
    protected $metadataAccessor;

    /**
     * @var MetadataRendererInterface
     */
    protected $metadataRenderer;

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
     * @param string $propertyPath
     *
     * @return mixed
     */
    public function getProperty(MetadataSubjectInterface $metadataSubject, $propertyPath)
    {
        return $this->metadataAccessor->getProperty($metadataSubject, $propertyPath);
    }

    /**
     * @param MetadataSubjectInterface $metadataSubject
     * @param string $propertyPath
     *
     * @return string
     */
    public function renderProperty(MetadataSubjectInterface $metadataSubject, $propertyPath)
    {
        $metadataProperty = $this->metadataAccessor->getProperty($metadataSubject, $propertyPath);

        return $this->metadataRenderer->render($metadataProperty);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_restricted_zone';
    }
}
