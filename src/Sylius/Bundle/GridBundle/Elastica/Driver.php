<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Elastica;

use Elastica\Search;
use Elastica\SearchableInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class Driver implements DriverInterface
{
    const NAME = 'elastica';

    /**
     * @var SearchableInterface
     */
    private $index;

    /**
     * @param SearchableInterface $entityManager
     */
    public function __construct(SearchableInterface $index)
    {
        $this->index = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $configuration, Parameters $parameters)
    {
        if (!array_key_exists('type', $configuration)) {
            throw new \InvalidArgumentException('"type" must be configured.');
        }

        $query = (array_key_exists('query', $configuration)) ?  $configuration['query'] : [];

        return new DataSource($this->index->getType($configuration['type']), $query);
    }
}
