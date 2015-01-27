<?php

namespace Sylius\Component\Report\Model;

interface ReportInterface
{
    public function getId();
    public function setId($id);
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
