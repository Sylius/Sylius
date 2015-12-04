<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Report\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ReportInterface extends CodeAwareInterface, ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getRenderer();

    /**
     * @param string $renderer
     */
    public function setRenderer($renderer);

    /**
     * @return array
     */
    public function getRendererConfiguration();

    /**
     * @param array $rendererConfiguration
     */
    public function setRendererConfiguration(array $rendererConfiguration);

    /**
     * @return string
     */
    public function getDataFetcher();

    /**
     * @param string $dataFetcher
     */
    public function setDataFetcher($dataFetcher);

    /**
     * @return array
     */
    public function getDataFetcherConfiguration();

    /**
     * @param array $dataFetcherConfiguration
     */
    public function setDataFetcherConfiguration(array $dataFetcherConfiguration);
}
