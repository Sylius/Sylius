<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Behat;

use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Bundle\SearchBundle\Command\IndexCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchContext extends DefaultContext
{
    /**
     * @Given /^I populate the index$/
     */
    public function iPopulateTheIndex()
    {
        $command = new IndexCommand();
        $command->setContainer($this->kernel->getContainer());

        $output = new BufferedOutput();
        if ($command->run(new ArgvInput(['env' => 'test']), $output)) { //return code is not zero
            throw new \RuntimeException($output->fetch());
        }
    }

    /**
     * @Given /^I select the "([^""]*)"$/
     */
    public function iSelectThe($id)
    {
        $html = $this->getSession()->getPage()->getHtml();

        $crawler = new Crawler($html);

        $element = $crawler->filter('#'.$id)->first();

        $element->addHtmlContent('checked');
    }

    /**
     * @Given /^I should find an indexed entry for "([^""]*)"$/
     */
    public function iCreateAndIndex($id)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from('Sylius\Bundle\SearchBundle\Model\SearchIndex', 'u')
            ->where('u.value LIKE :id')
            ->setParameter('id', '%'.$id.'%')
        ;

        $result = $queryBuilder->getQuery()->getResult();
        if (!$result) {
            throw new \Exception(
                "The entry does not exist in the index"
            );
        }

        return true;
    }

    /**
     * @Given /^I should not find an indexed entry for "([^""]*)"$/
     */
    public function iDeleteAnIndex($id)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select('u')
            ->from('Sylius\Bundle\SearchBundle\Model\SearchIndex', 'u')
            ->where('u.value LIKE :id')
            ->setParameter('id', '%'.$id.'%')
        ;

        $result = $queryBuilder->getQuery()->getResult();
        if (!empty($result)) {
            throw new \Exception(
                "The entry does exist in the index"
            );
        }

        return true;
    }
}
