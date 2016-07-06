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
use Sylius\Component\Metadata\Model\Custom\Page;
use Sylius\Component\Metadata\Model\Custom\PageMetadata;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;

/**
 * TODO: This file should be in Bundle/ not Component/
 *
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
            new \Twig_SimpleFunction('sylius_metadata_get', [$this, 'getProperty'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('sylius_metadata_render', [$this, 'renderProperty'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('sylius_metadata_render_page', [$this, 'renderPage'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param MetadataSubjectInterface $metadataSubject
     * @param string $type
     * @param string|null $propertyPath
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function getProperty(MetadataSubjectInterface $metadataSubject, $type, $propertyPath = null, $defaultValue = null)
    {
        return $this->metadataAccessor->getProperty($metadataSubject, $type, $propertyPath) ?: $defaultValue;
    }

    /**
     * @param MetadataSubjectInterface $metadataSubject
     * @param string|null $propertyPath
     * @param array $options
     *
     * @return string
     */
    public function renderProperty(MetadataSubjectInterface $metadataSubject, $type, $propertyPath = null, array $options = [])
    {
        $metadataProperty = $this->metadataAccessor->getProperty($metadataSubject, $type, $propertyPath);

        if (null === $metadataProperty) {
            return null;
        }

        return $this->metadataRenderer->render($metadataProperty, $options);
    }

    /**
     * @param MetadataSubjectInterface|null $metadataSubject
     * @param array $options
     *
     * @return null|string
     */
    public function renderPage(MetadataSubjectInterface $metadataSubject = null, array $options = [])
    {
        if (!$metadataSubject) {
            $metadataSubject = new Page();
        }

        $metadataProperty = $this->metadataAccessor->getProperty($metadataSubject, 'page');
        
        if (null === $metadataProperty) {
            if (!isset($options['values'])) {
                return null;
            }

            // Allow for the edge-case of no metadata defined at all but still passing values to render a page with
            $metadataProperty = new PageMetadata();
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
