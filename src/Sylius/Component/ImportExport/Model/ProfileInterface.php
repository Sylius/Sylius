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
interface ProfileInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getWriter();

    /**
     * @param string $writer
     */
    public function setWriter($writer);

    /**
     * @return array mixed
     */
    public function getWriterConfiguration();

    /**
     * @param array $writerConfiguration
     */
    public function setWriterConfiguration(array $writerConfiguration);

    /**
     * @return string
     */
    public function getReader();

    /**
     * @param string $reader
     */
    public function setReader($reader);

    /**
     * @return array
     */
    public function getReaderConfiguration();

    /**
     * @param array $readerConfiguration
     */
    public function setReaderConfiguration(array $readerConfiguration);

    /**
     * @param JobInterface $job
     */
    public function addJob(JobInterface $job);

    /**
     * @param JobInterface $job
     */
    public function removeJob(JobInterface $job);

    /**
     * @return Collection
     */
    public function getJobs();

    /**
     * @param Collection $jobs
     */
    public function setJobs(Collection $jobs);

    public function clearJobs();

    /**
     * @return integer
     */
    public function countJobs();

    /**
     * @param JobInterface $job
     *
     * @return boolean
     */
    public function hasJob(JobInterface $job);
}
