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
     * @Given /^there are following support requests:$/
     */
    public function thereAreContactRequests(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('support_request');

        foreach ($repository->findAll() as $contactRequest) {
            $manager->remove($contactRequest);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newContactRequest = $repository->createNew();
            $newContactRequest->setFirstName($data['firstName']);
            $newContactRequest->setLastName($data['lastName']);
            $newContactRequest->setEmail($data['email']);
            $newContactRequest->setMessage($data['message']);
            $newContactRequest->setTopic($data['topic']);

            $manager->persist($newContactRequest);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are following support topics:$/
     */
    public function thereAreContactTopics(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('support_topics');

        foreach ($repository->findAll() as $contactTopic) {
            $manager->remove($contactTopic);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newContactTopic = $repository->createNew();
            $newContactTopic->setTitle($data['title']);

            $manager->persist($newContactTopic);
        }

        $manager->flush();
    }
}
