<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\QueryLogger;

use Elastica\Document;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ElasticsearchQueryLogger implements QueryLoggerInterface
{
    private $type;

    /**
     * @var bool
     */
    private $isEnabled;

    public function __construct($type, $isEnabled)
    {
        $this->type = $type;
        $this->isEnabled = $isEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function logStringQuery($searchTerm, $ipAddress)
    {
        $document = new Document();
        $document->setData([
            'search_term' => $searchTerm,
            'ip_address' => $ipAddress,
        ]);
        $this->type->addDocuments([$document]);
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }
}
