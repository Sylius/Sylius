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
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Process\Process;

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
        $command = $this->kernel->getRootDir() . "/console sylius:search:index --env=test";
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        print $process->getOutput();
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
            ->from('Sylius\Bundle\SearchBundle\Entity\SearchIndex', 'u')
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
