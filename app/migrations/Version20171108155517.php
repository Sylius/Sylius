<?php

namespace Sylius\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\JsonArrayType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Exception;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductAttribute;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171108155517 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
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

        // migrate existing data
        $this->migrateData();

        // update column type to json_array
        $schema->getTable('sylius_product_attribute')->changeColumn('configuration', ['type' => JsonArrayType::getType(Type::JSON_ARRAY)]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // this migration can not be reverted
        $this->abortIf(true, 'The type of `configuration` field of `sylius_product_attribute` table can not be reverted.');
    }

    /**
     * Migrates the existing serialized data
     */
    protected function migrateData()
    {
        /** @var EntityRepository $productAttributeRepo */
        $productAttributeRepo = $this->container->get('sylius.repository.product_attribute');

        /** @var EntityManager $em */
        $em   = $this->container->get('doctrine.orm.default_entity_manager');
        $rows = $em->getConnection()->query('SELECT * FROM sylius_product_attribute')->fetchAll();

        foreach ($rows as $row) {
            $unserialized  = $this->performUnserialize($row['configuration']);

            /** @var ProductAttribute $productAttribute */
            $productAttribute = $productAttributeRepo->find($row['id']);

            $this->abortIf(!$productAttribute, sprintf('Product with id "%s" was not found', $row['id']));

            $productAttribute->setConfiguration($unserialized);
        }

        $em->flush();
    }

    /**
     * Tries to unserialize values of `configuration` filed of `sylius_product_attribute`, aborts the migration
     * in case of unserialization failure.
     *
     * @param string $configuration
     *
     * @return bool|mixed
     */
    protected function performUnserialize($configuration)
    {
        $data = false;

        try {
            $data = unserialize($configuration);
        } catch (Exception $e) {
            $this->abortIf(!is_array($data), 'Unable to unserialize() the configuration data from `sylius_product_attribute` table. The data might be in the wrong format.');
        }

        return $data;
    }
}
