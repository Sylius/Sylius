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

use Sylius\Component\Report\DataFetcher\DefaultDataFetchers;
use Sylius\Component\Report\Renderer\DefaultRenderers;

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class Report implements ReportInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $code;

    /**
     * Renderer name.
     *
     * @var string
     */
    protected $renderer = DefaultRenderers::TABLE;

    /**
     * @var array
     */
    protected $rendererConfiguration = array();

    /**
     * Data fetcher name.
     *
     * @var string
     */
    protected $dataFetcher = DefaultDataFetchers::USER_REGISTRATION;

    /**
     * @var array
     */
    protected $dataFetcherConfiguration = array();

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFetcher()
    {
        return $this->dataFetcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFetcher($dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;
    }

    /**
     * @return string
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFetcherConfiguration()
    {
        return $this->dataFetcherConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setDataFetcherConfiguration(array $dataFetcherConfiguration)
    {
        $this->dataFetcherConfiguration = $dataFetcherConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getRendererConfiguration()
    {
        return $this->rendererConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setRendererConfiguration(array $rendererConfiguration)
    {
        $this->rendererConfiguration = $rendererConfiguration;
    }
}
