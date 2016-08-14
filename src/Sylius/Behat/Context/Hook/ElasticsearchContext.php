<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use FOS\ElasticaBundle\Index\IndexManager;
use FOS\ElasticaBundle\Index\Resetter;
use FOS\ElasticaBundle\Provider\ProviderRegistry;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
final class ElasticsearchContext implements Context
{
    /**
     * @var Index
     */
    private $indexManager;

    /**
     * @var ProviderRegistry
     */
    private $providerRegistry;

    /**
     * @var Resetter
     */
    private $resetter;

    /**
     * @param IndexManager $indexManager
     * @param ProviderRegistry $providerRegistry
     * @param Resetter $resetter
     */
    public function __construct(IndexManager $indexManager, ProviderRegistry $providerRegistry, Resetter $resetter)
    {
        $this->indexManager = $indexManager;
        $this->providerRegistry = $providerRegistry;
        $this->resetter = $resetter;
    }

    /**
     * @BeforeScenario @elasticsearch
     */
    public function resetIndex()
    {
        if (null === $this->indexManager) {
            throw new \RuntimeException('Cannot purge index. Index is not set');
        }

        $indexes = array_keys($this->indexManager->getAllIndexes());

        foreach ($indexes as $index) {
            $this->resetter->resetIndex($index, true);

            $types = array_keys($this->providerRegistry->getIndexProviders($index));
            foreach ($types as $type) {
                $this->resetter->resetIndexType($index, $type);

                $provider = $this->providerRegistry->getProvider($index, $type);
                $provider->populate();
            }

            $this->indexManager->getIndex($index)->refresh();
        }
    }
}
