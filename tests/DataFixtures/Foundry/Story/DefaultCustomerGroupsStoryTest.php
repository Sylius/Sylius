<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\DataFixtures\Foundry\Story;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultCustomerGroupsStory;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultCustomerGroupsStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_customer_groups(): void
    {
        self::bootKernel();

        DefaultCustomerGroupsStory::load();

        $customerGroups = $this->getCustomerGroupRepository()->findAll();

        $this->assertCount(2, $customerGroups);

        $customerGroup = $this->findCustomerGroupByCode('retail');
        $this->assertEquals('Retail', $customerGroup->getName());

        $customerGroup = $this->findCustomerGroupByCode('wholesale');
        $this->assertEquals('Wholesale', $customerGroup->getName());
    }

    private function findCustomerGroupByCode(string $code): CustomerGroupInterface
    {
        $customerGroup = $this->getCustomerGroupRepository()->findOneBy(['code' => $code]);

        $this->assertNotNull($customerGroup, sprintf('Customer group %s was not found.', 'retail'));

        return $customerGroup;
    }

    private function getCustomerGroupRepository(): RepositoryInterface
    {
        return self::getContainer()->get('sylius.repository.customer_group');
    }
}
