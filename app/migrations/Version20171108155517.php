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
use Sylius\Component\Shipping\Model\ShippingMethod;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171108155517 extends AbstractMigration implements ContainerAwareInterface
{
    const TABLE_PRODUCT_ATTRIBUTE = 'sylius_product_attribute';

    const TABLE_SHIPPING_METHOD = 'sylius_shipping_method';

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

        /** @var EntityRepository $productAttributeRepo */
        $productAttributeRepo = $this->container->get('sylius.repository.product_attribute');

        /** @var EntityRepository $shippingMethodRepo */
        $shippingMethodRepo = $this->container->get('sylius.repository.shipping_method');

        // migrate data in `sylius_product_attribute` table
        $this->migrateData($productAttributeRepo, self::TABLE_PRODUCT_ATTRIBUTE);

        // migrate data in `sylius_shipping_method` table
        $this->migrateData($shippingMethodRepo, self::TABLE_SHIPPING_METHOD);

        // update column type to json_array
        $schema->getTable(self::TABLE_PRODUCT_ATTRIBUTE)->changeColumn('configuration', ['type' => JsonArrayType::getType(Type::JSON_ARRAY)]);
        $schema->getTable(self::TABLE_SHIPPING_METHOD)->changeColumn('configuration', ['type' => JsonArrayType::getType(Type::JSON_ARRAY)]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // this migration can not be reverted
        $this->abortIf(true, 'The type of `configuration` field of `sylius_product_attribute` and `sylius_shipping_method` tables can not be reverted.');
    }

    /**
     * Migrates the existing serialized data
     */
    protected function migrateData(EntityRepository $entityRepository, string $table)
    {
        /** @var EntityManager $em */
        $em   = $this->container->get('doctrine.orm.default_entity_manager');
        $rows = $em->getConnection()->query(sprintf("SELECT `id`, `configuration` FROM `%s`", $table))->fetchAll();

        foreach ($rows as $row) {
            $unserialized  = $this->performUnserialize($row['configuration']);

            /** @var ProductAttribute|ShippingMethod|null $entity */
            $entity = $entityRepository->find($row['id']);

            $this->abortIf(!$entity, sprintf('Entity with id "%s" was not found in the `%s` table.', $row['id'], $table));

            $entity->setConfiguration($unserialized);
        }

        $em->flush();
    }

    /**
     * Tries to unserialize values of `configuration` field, aborts the migration in case of unserialization failure.
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
            $this->abortIf(!is_array($data), 'Unable to unserialize() the configuration data from the table. The data might be in the wrong format.');
        }

        return $data;
    }
}
