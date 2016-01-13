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
final class CompositeMetadataRenderer implements MetadataRendererInterface
{
    /**
     * @var MetadataRendererInterface[]
     */
    private $renderers;

    /**
     * @param MetadataRendererInterface[] $renderers
     */
    public function __construct(array $renderers = [])
    {
        $this->assertRenderersHaveCorrectType($renderers);

        $this->renderers = $renderers;
    }

    /**
     * {@inheritdoc}
     */
    public function render(MetadataInterface $metadata, array $options = [])
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($metadata, $options)) {
                return $renderer->render($metadata, $options);
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
    public function supports(MetadataInterface $metadata, array $options = [])
    {
        foreach ($this->renderers as $renderer) {
            if ($renderer->supports($metadata, $options)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param MetadataRendererInterface[] $renderers
     */
    private function assertRenderersHaveCorrectType(array $renderers)
    {
        foreach ($renderers as $renderer) {
            if ($renderer instanceof MetadataRendererInterface) {
                continue;
            }

            throw new \InvalidArgumentException(sprintf(
                'Metadata renderer should have type "%s"',
                MetadataRendererInterface::class
            ));
        }
    }
}
