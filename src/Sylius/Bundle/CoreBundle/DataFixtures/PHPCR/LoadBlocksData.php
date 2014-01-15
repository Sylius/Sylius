<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use PHPCR\Util\NodeHelper;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Yaml\Parser;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;

class LoadBlocksData extends ContainerAware implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $session = $manager->getPhpcrSession();

        $basepath = $this->container->getParameter('cmf_block.persistence.phpcr.block_basepath');;
        NodeHelper::createPath($session, $basepath);

        $parent = $manager->find(null, $basepath);

        $contactBlock = $this->container->get('sylius.repository.block')->createNew();
        $contactBlock->setParentDocument($parent);
        $contactBlock->setName('contact');
        $contactBlock->setTitle('Contact us');
        $contactBlock->setBody('Call (8) 888 333 421!');

        $manager->persist($contactBlock);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
