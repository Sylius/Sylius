<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170921111000 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $customerTaxCategoryFactory = $this->container->get('sylius.factory.customer_tax_category');
        $customerTaxCategoryRepository = $this->container->get('sylius.repository.customer_tax_category');
        $taxRateRepository = $this->container->get('sylius.repository.tax_rate');
        $channelRepository = $this->container->get('sylius.repository.channel');
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var CustomerTaxCategoryInterface $customerTaxCategory */
        $customerTaxCategory = $customerTaxCategoryFactory->createNew();
        $customerTaxCategory->setName('General');
        $customerTaxCategory->setCode('general');

        $customerTaxCategoryRepository->add($customerTaxCategory);

        /** @var ChannelInterface $channel */
        foreach ($channelRepository->findAll() as $channel) {
            $channel->setDefaultCustomerTaxCategory($customerTaxCategory);
        }

        /** @var TaxRateInterface $taxRate */
        foreach ($taxRateRepository->findAll() as $taxRate) {
            $taxRate->setCustomerTaxCategory($customerTaxCategory);
        }

        $entityManager->flush();

        $this->addSql('ALTER TABLE sylius_tax_rate ADD CONSTRAINT FK_3CD86B2EE6D0D277 FOREIGN KEY (customer_tax_category_id) REFERENCES sylius_customer_tax_category (id)');
        $this->addSql('CREATE INDEX IDX_3CD86B2EE6D0D277 ON sylius_tax_rate (customer_tax_category_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_customer_group DROP tax_category_id');
        $this->addSql('DROP INDEX IDX_3CD86B2EE6D0D277 ON sylius_tax_rate');
    }
}
