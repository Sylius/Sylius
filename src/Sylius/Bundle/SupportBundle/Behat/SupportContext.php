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
    public function thereAreSupportRequests(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('support_request');
        $topicRepository = $this->getRepository('support_topic');

        foreach ($repository->findAll() as $supportRequest) {
            $manager->remove($supportRequest);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newSupportRequest = $repository->createNew();
            $newSupportRequest->setFirstName($data['firstName']);
            $newSupportRequest->setLastName($data['lastName']);
            $newSupportRequest->setEmail($data['email']);
            $newSupportRequest->setMessage($data['message']);

            $newSupportRequest->setTopic($topicRepository->findOneBy(array('title' => $data['topic'])));

            $manager->persist($newSupportRequest);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are following support topics:$/
     */
    public function thereAreSupportTopics(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('support_topic');

        foreach ($repository->findAll() as $supportTopic) {
            $manager->remove($supportTopic);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newSupportTopic = $repository->createNew();
            $newSupportTopic->setTitle($data['title']);

            $manager->persist($newSupportTopic);
        }

        $manager->flush();
    }

}
