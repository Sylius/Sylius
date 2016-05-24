<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Uploader;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChunkingPathGenerator implements PathGeneratorInterface
{
    /**
     * @var PathGeneratorInterface
     */
    private $decoratedPathGenerator;

    /**
     * @var int
     */
    private $numberOfChunks;

    /**
     * @var int
     */
    private $chunkLength;

    /**
     * @param PathGeneratorInterface $decoratedPathGenerator
     * @param int $numberOfChunks
     * @param int $chunkLength
     */
    public function __construct(PathGeneratorInterface $decoratedPathGenerator, $numberOfChunks, $chunkLength)
    {
        $this->decoratedPathGenerator = $decoratedPathGenerator;
        $this->numberOfChunks = $numberOfChunks;
        $this->chunkLength = $chunkLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(\SplFileInfo $file)
    {
        foreach ($this->decoratedPathGenerator->generate($file) as $path) {
            yield $this->getChunkedPath($path);
        }
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getChunkedPath($path)
    {
        for ($i = 1; $i <= $this->numberOfChunks; ++$i) {
            $cutAt = $i * 2 + ($i - 1);

            $path = substr($path, 0, $cutAt) . '/' . substr($path, $cutAt);
        }

        return $path;
    }
}
