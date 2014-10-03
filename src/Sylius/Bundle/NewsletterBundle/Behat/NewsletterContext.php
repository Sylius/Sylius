<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\NewsletterBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

class NewsletterContext extends DefaultContext
{
    /**
     * @Given /^there are following subscription lists:$/
     */
    public function thereAreSubscriptionLists(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('subscription_list');

        foreach ($repository->findAll() as $subscriptionList) {
            $manager->remove($subscriptionList);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newSubscriptionList = $repository->createNew();
            $newSubscriptionList->setName($data['name']);
            $newSubscriptionList->setDescription($data['description']);

            $manager->persist($newSubscriptionList);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are following subscribers:$/
     */
    public function thereAreSubscribers(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('subscriber');

        foreach ($repository->findAll() as $subscriber) {
            $manager->remove($subscriber);
        }

        $manager->flush();

        foreach ($table->getHash() as $data) {
            $newSubscriber = $repository->createNew();
            $newSubscriber->setEmail($data['email']);

            $manager->persist($newSubscriber);
        }

        $manager->flush();
    }

}
