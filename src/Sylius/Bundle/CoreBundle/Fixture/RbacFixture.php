<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\RbacBundle\Doctrine\RbacInitializer;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RbacFixture extends AbstractFixture
{
    /**
     * @var RbacInitializer
     */
    private $initializer;

    /**
     * @param RbacInitializer $initializer
     */
    public function __construct(RbacInitializer $initializer)
    {
        $this->initializer = $initializer;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        $this->initializer->initialize();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rbac';
    }
}
