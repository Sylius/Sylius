<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Setup\Step;

use Sylius\Bundle\FlowBundle\Setup\SetupInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;

interface StepInterface
{
    function execute();
    function complete();
    function isCompleted();
    function skip();
    function isSkipped();
    function getSetup();
    function setSetup(SetupInterface $setup);
    function getStorage();
    function setStorage(StorageInterface $storage);
    function getIndex();
    function setIndex($index);
    function getPrevious();
    function setPrevious(StepInterface $step);
    function hasPrevious();
    function getNext();
    function setNext(StepInterface $step);
    function hasNext();
}
