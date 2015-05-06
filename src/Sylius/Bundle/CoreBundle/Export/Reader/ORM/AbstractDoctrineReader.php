<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Export\Reader\ORM;

use Monolog\Logger;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Reader\ReaderInterface;

/**
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 * @author Łukasz Chruściel <luksza.chrusciel@lakion.com>
 */
abstract class AbstractDoctrineReader implements ReaderInterface
{
    /**
     * @var \ArrayIterator
     */
    private $results;
    /**
     * @var boolean
     */
    private $running = false;
    /**
     * @var integer
     */
    protected $resultCode = 0;
    /**
     * @var integer
     */
    protected $batchSize;

    /**
     * {@inheritdoc}
     */
    public function read(array $configuration, Logger $logger)
    {
        if (!$this->running)
        {
            $this->running = true;
            $this->results = new \ArrayIterator($this->getQuery()->execute());
            $this->batchSize = $configuration['batch_size'];
        }

        $results = array();

        for ($i = 0; $i < $this->batchSize; $i++) {
            if ($result = $this->results->current()) {
                $this->results->next();
            }

            $result = $this->process($result);
            $results[] = $result;
        }

        return $results;
    }

    /**
     * Process given array into a database mapped object.
     *
     * @param $result
     *
     * @return array
     */
    protected abstract function process($result);

    /**
     * Provides list of all suited objects
     *
     * @return mixed
     */
    protected abstract function getQuery();

    /**
     * {@inheritdoc}
     */
    public function finalize(JobInterface $job)
    {
        $job->addMetadata(array('result_code' => $this->resultCode));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultCode()
    {
        return $this->resultCode;
    }
}
