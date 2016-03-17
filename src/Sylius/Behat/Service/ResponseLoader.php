<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
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

    /**
     * @return string
     */
    private function getResponsesFolder()
    {
        return $this->getCalledClassFolder() . '/Responses';
    }

    /**
     * @return string
     */
    private function getMockedResponsesFolder()
    {
        return $this->getResponsesFolder() . '/Mocked';
    }

    /**
     * @return string
     */
    private function getExpectedResponsesFolder()
    {
        return $this->getResponsesFolder() . '/Expected';
    }

    /**
     * @return string
     */
    private function getCalledClassFolder()
    {
        $calledClass = get_called_class();
        $calledClassFolder = dirname((new \ReflectionClass($calledClass))->getFileName());

        return $calledClassFolder;
    }

    /**
     * @param string $source
     *
     * @throws \RuntimeException
     */
    private function assertSourceExists($source)
    {
        if (!file_exists($source)) {
            throw new \RuntimeException(sprintf('File %s does not exist', $source));
        }
    }

    /**
     * @param string $source
     * @param mixed $content
     *
     * @throws \RuntimeException
     */
    private function assertContentIsNotEmpty($source, $content)
    {
        if ('' === $content) {
            throw new \RuntimeException(sprintf('Something went wrong, file %s is empty', $source));
        }
    }

    /**
     * @param string $source
     * @param mixed $content
     *
     * @throws \RuntimeException
     */
    private function assertContentIsProperLoaded($source, $content)
    {
        if (false === $content) {
            throw new \RuntimeException(sprintf('Something went wrong, cannot open %s', $source));
        }
    }

    /**
     * @param string $source
     *
     * @throws \RuntimeException
     */
    private function assertSourceIsNotFolder($source)
    {
        if (true === is_dir($source)) {
            throw new \RuntimeException(sprintf('Given source %s is a folder!', $source));
        }
    }

    /**
     * @param string $source
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function getFileContents($source)
    {
        $this->assertSourceExists($source);
        $this->assertSourceIsNotFolder($source);
        $content = file_get_contents($source, true);

        $this->assertContentIsProperLoaded($source, $content);
        $this->assertContentIsNotEmpty($source, $content);

        return $content;
    }
}
