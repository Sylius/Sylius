<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\PHPCR;

use Doctrine\Common\Persistence\ObjectManager;
use PHPCR\Util\NodeHelper;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

class LoadBlocksData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $productOptionManager)
    {
        $session = $productOptionManager->getPhpcrSession();

        $basepath = $this->container->getParameter('cmf_block.persistence.phpcr.block_basepath');
        NodeHelper::createPath($session, $basepath);

        $parent = $productOptionManager->find(null, $basepath);
        $repository = $this->container->get('sylius.repository.simple_block');

        $contactBlock = $repository->createNew();
        $contactBlock->setParentDocument($parent);
        $contactBlock->setName('contact');
        $contactBlock->setTitle('Contact us');
        $contactBlock->setBody('<p>Call us '.$this->faker->phoneNumber.'!</p><p>'.$this->faker->paragraph.'</p>');

        $productOptionManager->persist($contactBlock);

        for ($i = 1; $i <= 3; $i++) {
            $block = $repository->createNew();
            $block->setParentDocument($parent);
            $block->setName('block-'.$i);
            $block->setTitle(ucfirst($this->faker->word));
            $block->setBody($this->faker->paragraph);

            $productOptionManager->persist($block);
        }

        $repository = $this->container->get('sylius.repository.string_block');

        $welcomeText = $repository->createNew();
        $welcomeText->setParentDocument($parent);
        $welcomeText->setName('welcome-text');
        $welcomeText->setBody('Welcome to Sylius e-commerce');

        $productOptionManager->persist($welcomeText);

        $productOptionManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
