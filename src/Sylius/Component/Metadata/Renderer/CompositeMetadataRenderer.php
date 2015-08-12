<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Renderer;

use Sylius\Component\Metadata\Model\MetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CompositeMetadataRenderer implements MetadataRendererInterface
{
    /**
     * @var MetadataRendererInterface[]
     */
    protected $renderers = [];

    /**
     * @param MetadataRendererInterface[] $renderers
     */
    public function __construct(array $renderers = [])
    {
        foreach ($renderers as $renderer) {
            $this->addRenderer($renderer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render(MetadataInterface $metadata)
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($metadata)) {
                return $renderer->render($metadata);
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'There is no renderer suitable for %s',
            get_class($metadata)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataInterface $metadata)
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($metadata)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param MetadataRendererInterface $renderer
     */
    protected function addRenderer(MetadataRendererInterface $renderer)
    {
        $this->renderers[] = $renderer;
    }
}
