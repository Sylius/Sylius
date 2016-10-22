<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StringBlockContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $factory
     * @param ObjectManager $manager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $factory,
        ObjectManager $manager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->factory = $factory;
        $this->manager = $manager;
    }

    /**
     * @Given the store has string block :name
     */
    public function theStoreHasStringBlock($name)
    {
        $block = $this->factory->createNew();

        $block->setName($name);
        $block->setBody('Testing');

        $this->manager->persist($block);
        $this->manager->flush();
    }

    /**
     * @Given the store has string block :name with body :body
     */
    public function theStoreHasStringBlockWithBody($name, $body)
    {
        $block = $this->factory->createNew();

        $block->setName($name);
        $block->setBody($body);

        $this->manager->persist($block);
        $this->manager->flush();

        $this->sharedStorage->set('string_block', $block);
    }
}
