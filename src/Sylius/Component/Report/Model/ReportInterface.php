<?php

namespace Sylius\Component\Report\Model;

interface ReportInterface
{
    public function getId();
    public function setId($id);
    public function getName();
    public function setName($name);
    public function getDescription();
    public function setDescription($description);
}