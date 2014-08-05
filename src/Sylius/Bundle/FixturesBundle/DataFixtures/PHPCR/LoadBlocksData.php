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
    public function load(ObjectManager $manager)
    {
        $session = $manager->getPhpcrSession();

        $basepath = $this->container->getParameter('cmf_block.persistence.phpcr.block_basepath');
        NodeHelper::createPath($session, $basepath);

        $parent = $manager->find(null, $basepath);
        $blockManager = $this->getManager('simple_block');

        $contactBlock = $blockManager->createNew();
        $contactBlock->setParentDocument($parent);
        $contactBlock->setName('contact');
        $contactBlock->setTitle('Contact us');
        $contactBlock->setBody('<p>Call us '.$this->faker->phoneNumber.'!</p><p>'.$this->faker->paragraph.'</p>');

        $manager->persist($contactBlock);

        for ($i = 1; $i <= 3; $i++) {
            $block = $blockManager->createNew();
            $block->setParentDocument($parent);
            $block->setName('block-'.$i);
            $block->setTitle(ucfirst($this->faker->word));
            $block->setBody($this->faker->paragraph);

            $manager->persist($block);
        }

        $blockManager = $this->getManager('string_block');

        $welcomeText = $blockManager->createNew();
        $welcomeText->setParentDocument($parent);
        $welcomeText->setName('welcome-text');
        $welcomeText->setBody('Welcome to Sylius e-commerce');

        $manager->persist($welcomeText);

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
