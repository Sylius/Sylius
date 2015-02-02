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
     *@var integer
     */
    private $id;

    /**
     * @var String
     */
    private $name;

    /**
     * @var String
     */
    private $description;

    /**
     * @var String
     */
    private $code;

    /**
     * Renderer name.
     *
     * @var string
     */
    private $renderer = DefaultRenderers::TABLE;

    /**
     * Renderer configuration.
     *
     * @var array
     */
    private $rendererConfiguration = array();

    /**
     * Data fetcher name.
     *
     * @var string
     */
    private $dataFetcher = DefaultDataFetchers::USER_REGISTRATION;

    /**
     * @var Array
     */
    private $dataFetcherConfiguration = array();

    /**
     * Gets the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param integer $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the value of code.
     *
     * @param string $code the code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Gets the dataFetcher name.
     *
     * @return DataFetcher
     */
    public function getDataFetcher()
    {
        return $this->dataFetcher;
    }

    /**
     * Sets the value of dataFetcher.
     *
     * @param DataFetcher $dataFetcher the data fetcher
     *
     * @return self
     */
    public function setDataFetcher($dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;
    }

    /**
     * Gets the Renderer name.
     *
     * @return string
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Sets the Renderer name.
     *
     * @param string $renderer the renderer
     *
     * @return self
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * Gets the value of dataFetcherConfiguration.
     *
     * @return Array
     */
    public function getDataFetcherConfiguration()
    {
        return $this->dataFetcherConfiguration;
    }

    /**
     * Sets the value of dataFetcherConfiguration.
     *
     * @param Array $dataFetcherConfiguration the data fetcher configuration
     *
     * @return self
     */
    public function setDataFetcherConfiguration(array $dataFetcherConfiguration)
    {
        $this->dataFetcherConfiguration = $dataFetcherConfiguration;
    }

    /**
     * Gets the Renderers configuration.
     *
     * @return array
     */
    public function getRendererConfiguration()
    {
        return $this->rendererConfiguration;
    }

    /**
     * Sets the Renderers configuration.
     *
     * @param array $rendererConfiguration the renderer configuration
     *
     * @return self
     */
    public function setRendererConfiguration($rendererConfiguration)
    {
        $this->rendererConfiguration = $rendererConfiguration;

        return $this;
    }
}
