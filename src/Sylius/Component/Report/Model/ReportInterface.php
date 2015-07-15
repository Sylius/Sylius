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

interface ReportInterface
{
    public function getId();
    public function getName();
    public function setName($name);
    public function getDescription();
    public function setDescription($description);
    public function getRenderer();
    public function setRenderer($renderer);
    public function getRendererConfiguration();
    public function setRendererConfiguration($rendererConfiguration);
    public function getDataFetcher();
    public function setDataFetcher($dataFetcher);
    public function getDataFetcherConfiguration();
    public function setDataFetcherConfiguration(array $dataFetcherConfiguration);
}
