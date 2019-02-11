<?php

declare(strict_types=1);

namespace Sylius\Tests\Command;

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class SyliusFixturesTest extends KernelTestCase
{
    /** @test */
    public function fixtures_are_loaded_properly(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('sylius:fixtures:load');
        (new CommandTester($command))->execute(['suite' => 'test'], ['interactive' => false]);

        $productRepository = $kernel->getContainer()->get('sylius.repository.product');

        /** @var ProductInterface $product */
        $product = $productRepository->findOneByCode('LOTR1');
        $this->assertNotNull($product);

        $publicationDateAttribute = $product->getAttributeByCodeAndLocale('PUBLICATION_DATE', 'en_US');
        $this->assertEquals(new \DateTime('19-07-1954'), $publicationDateAttribute->getValue());
    }
}
