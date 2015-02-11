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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProfileInterface
{
    public function getId();
    public function getName();
    public function setName($name);
    public function getCode();
    public function setCode($code);
    public function getDescription();
    public function setDescription($description);
    public function getWriter();
    public function setWriter($writer);
    public function getWriterConfiguration();
    public function setWriterConfiguration(array $writerConfiguration);
    public function getReader();
    public function setReader($reader);
    public function getReaderConfiguration();
    public function setReaderConfiguration(array $readerConfiguration);
    public function addJob(JobInterface $job);
    public function removeJob(JobInterface $job);
}