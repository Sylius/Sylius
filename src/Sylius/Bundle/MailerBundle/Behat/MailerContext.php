<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MailerBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

class MailerContext extends DefaultContext
{
    /**
     * @Given /^there are following emails configured:$/
     * @Given /^the following emails exist:$/
     */
    public function thereAreEmails(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $email = $this->getFactory('email')->createNew();

            $email->setCode(trim($data['code']));
            $email->setSubject(trim($data['subject']));
            $email->setEnabled('yes' === trim($data['enabled']));
            $email->setContent('Testing!');

            $manager = $this->getEntityManager();
            $manager->persist($email);
        }

        $this->getEntityManager()->flush();
    }
}
