<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service;

class ResponseLoader implements ResponseLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMockedResponse($source)
    {
        $source = $this->getMockedResponsesFolder() . '/' . $source;

        return (array) json_decode($this->getFileContents($source));
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedResponse($source)
    {
        $source = $this->getExpectedResponsesFolder() . '/' . $source;

        return (array) json_decode($this->getFileContents($source));
    }

    private function getResponsesFolder(): string
    {
        return $this->getCalledClassFolder() . '/Responses';
    }

    private function getMockedResponsesFolder(): string
    {
        return $this->getResponsesFolder() . '/Mocked';
    }

    private function getExpectedResponsesFolder(): string
    {
        return $this->getResponsesFolder() . '/Expected';
    }

    private function getCalledClassFolder(): string
    {
        $calledClass = get_called_class();
        $calledClassFolder = dirname((new \ReflectionClass($calledClass))->getFileName());

        return $calledClassFolder;
    }

    /**
     * @throws \RuntimeException
     */
    private function assertSourceExists(string $source): void
    {
        if (!file_exists($source)) {
            throw new \RuntimeException(sprintf('File %s does not exist', $source));
        }
    }

    /**
     * @param mixed $content
     *
     * @throws \RuntimeException
     */
    private function assertContentIsNotEmpty(string $source, $content): void
    {
        if ('' === $content) {
            throw new \RuntimeException(sprintf('Something went wrong, file %s is empty', $source));
        }
    }

    /**
     * @param mixed $content
     *
     * @throws \RuntimeException
     */
    private function assertContentIsProperLoaded(string $source, $content): void
    {
        if (false === $content) {
            throw new \RuntimeException(sprintf('Something went wrong, cannot open %s', $source));
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function assertSourceIsNotFolder(string $source): void
    {
        if (true === is_dir($source)) {
            throw new \RuntimeException(sprintf('Given source %s is a folder!', $source));
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function getFileContents(string $source): string
    {
        $this->assertSourceExists($source);
        $this->assertSourceIsNotFolder($source);
        $content = file_get_contents($source, true);

        $this->assertContentIsProperLoaded($source, $content);
        $this->assertContentIsNotEmpty($source, $content);

        return $content;
    }
}
