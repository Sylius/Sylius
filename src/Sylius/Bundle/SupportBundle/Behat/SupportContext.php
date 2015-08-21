<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SupportBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

class SupportContext extends DefaultContext
{
    /**
     * @Given /^there are following support tickets:$/
     */
    public function thereAreSupportTickets(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('support_ticket');
        $categoryRepository = $this->getRepository('support_category');

        foreach ($repository->findAll() as $supportTicket) {
            $manager->remove($supportTicket);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newSupportTicket = $repository->createNew();
            $newSupportTicket->setFirstName($data['firstName']);
            $newSupportTicket->setLastName($data['lastName']);
            $newSupportTicket->setEmail($data['email']);
            $newSupportTicket->setMessage($data['message']);

            $newSupportTicket->setCategory($categoryRepository->findOneBy(array('title' => $data['category'])));

            $manager->persist($newSupportTicket);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are following support categories:$/
     */
    public function thereAreSupportCategories(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('support_category');

        foreach ($repository->findAll() as $supportCategory) {
            $manager->remove($supportCategory);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newSupportCategory = $repository->createNew();
            $newSupportCategory->setTitle($data['title']);

            $manager->persist($newSupportCategory);
        }

        $manager->flush();
    }

}
