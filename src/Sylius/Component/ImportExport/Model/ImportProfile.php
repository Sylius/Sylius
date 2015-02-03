<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Model;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ImportProfile extends Profile implements ExportProfileInterface
{
    function __construct() 
    {
        $this->reader = 'csv_reader';
        $this->readerConfiguration = array();
        $this->writer = 'product_writer';
        $this->writerConfiguration = array();
    }

    /**
     * {@inheritdoc}
     */
    public function addJob(JobInterface $job)
    {
        if ($this->hasJob($job)) {
            return $this;
        }

        foreach ($this->jobs as $existingJob) {
            if ($job->equals($existingJob)) {
                $existingJob->merge($job, false);

                return $this;
            }
        }

        $job->setImportProfile($this);
        $this->jobs->add($job);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeJob(JobInterface $job)
    {
        if ($this->hasJob($job)) {
            $job->setImportProfile(null);
            $this->jobs->removeElement($job);
        }

        return $this;
    }
}