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
 * @author agounaris <agounaris@gmail.com>
 */
class ElasticsearchQueryLogger implements QueryLoggerInterface
{
    /**
     * @var
     */
    protected $type;

    /**
     * @var
     */
    private $isEnabled;

    /**
     * @param $type
     * @param $isEnabled
     */
    public function __construct($type, $isEnabled)
    {
        $this->type = $type;
        $this->isEnabled = $isEnabled;
    }

    /**
     * @param $searchTerm
     * @param $ipAddress
     */
    public function logStringQuery($searchTerm, $ipAddress)
    {
        $document = new Document();
        $document->setData(array(
            'search_term' => $searchTerm,
            'ip_address' => $ipAddress
        ));
        $this->type->addDocuments(array($document));
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }
}