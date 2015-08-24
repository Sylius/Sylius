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

use Doctrine\Common\Collections\Collection;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
abstract class Profile implements ProfileInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $writer;

    /**
     * @var array
     */
    protected $writerConfiguration = array();

    /**
     * @var string
     */
    protected $reader;

    /**
     * @var array
     */
    protected $readerConfiguration = array();

    /**
     * Profile jobs.
     *
     * @var Collection|JobInterface[]
     */
    protected $jobs;

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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * {@inheritdoc}
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * {@inheritdoc}
     */
    public function setWriter($writer)
    {
        $this->writer = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function getWriterConfiguration()
    {
        return $this->writerConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setWriterConfiguration(array $writerConfiguration)
    {
        $this->writerConfiguration = $writerConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * {@inheritdoc}
     */
    public function setReader($reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function getReaderConfiguration()
    {
        return $this->readerConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setReaderConfiguration(array $readerConfiguration)
    {
        $this->readerConfiguration = $readerConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * {@inheritdoc}
     */
    public function setJobs(Collection $jobs)
    {
        $this->jobs = $jobs;
    }

    /**
     * {@inheritdoc}
     */
    public function clearJobs()
    {
        $this->jobs->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function countJobs()
    {
        return $this->jobs->count();
    }

    /**
     * {@inheritdoc}
     */
    public function hasJob(JobInterface $job)
    {
        return $this->jobs->contains($job);
    }

    /**
     * {@inheritdoc}
     */
    public function addJob(JobInterface $job)
    {
        if ($this->hasJob($job)) {
            return;
        }

        foreach ($this->jobs as $existingJob) {
            if ($job === $existingJob) {
                $existingJob->merge($job, false);

                return;
            }
        }

        $job->setProfile($this);
        $this->jobs->add($job);
    }

    /**
     * {@inheritdoc}
     */
    public function removeJob(JobInterface $job)
    {
        $this->jobs->removeElement($job);
    }
}
