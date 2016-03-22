<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class SharedStorageContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^(it|its|theirs)$/
     */
    public function getLatestResource()
    {
        return $this->sharedStorage->getLatestResource();
    }

    /**
     * @Transform /^(?:this|that|the) ([^"]+)$/
     */
    public function getResource($resource)
    {
        return $this->sharedStorage->get(str_replace(' ', '_', $resource));
    }
}
