<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContactBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Contact\Model\RequestInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

class ContactContext extends DefaultContext
{
    /**
     * @Given /^there are following contact requests:$/
     */
    public function thereAreContactRequests(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('contact_request');

        foreach ($repository->findAll() as $contactRequest) {
            $manager->remove($contactRequest);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            /* @var $contactRequest RequestInterface */
            $contactRequest = $repository->createNew();
            $contactRequest->setCustomer($this->createCustomer($data));
            $contactRequest->setMessage($data['message']);
            $contactRequest->setTopic($data['topic']);

            $manager->persist($contactRequest);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are following contact topics:$/
     */
    public function thereAreContactTopics(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('contact_topics');

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

    public function createCustomer(array $data)
    {
        if (null === $customer = $this->getRepository('customer')->findOneBy(array('email' => $data['email']))) {
            /* @var $customer CustomerInterface */
            $customer = $this->getRepository('customer')->createNew();
            $customer->setFirstname(isset($data['firstName']) ? $data['firstName'] : $this->faker->firstName);
            $customer->setLastname(isset($data['lastName']) ? $data['lastName'] : $this->faker->lastName);
            $customer->setEmail($data['email']);

            $this->getEntityManager()->persist($customer);
            $this->getEntityManager()->flush();
        }

        return $customer;
    }
}
