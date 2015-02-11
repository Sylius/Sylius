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
    protected $writerConfiguration;

    /**
     * @var string
     */
    protected $reader;

    /**
     * @var array
     */
    protected $readerConfiguration;

    /**
     * Jobs of profile.
     *
     * @var Collection|JobInterface[]
     */
    protected $jobs;

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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearJobs()
    {
        $this->jobs->clear();

        return $this;
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
            return $this;
        }

        foreach ($this->jobs as $existingJob) {
            if ($job->equals($existingJob)) {
                $existingJob->merge($job, false);

                return $this;
            }
        }

        $job->setProfile($this);
        $this->jobs->add($job);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeJob(JobInterface $job)
    {
        if ($this->hasJob($job)) {
            $job->setProfile(null);
            $this->jobs->removeElement($job);
        }

        return $this;
    }
}
