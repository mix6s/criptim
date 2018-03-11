<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180307155752 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE local_to_bittrex_exchange_order (id BIGINT NOT NULL, order_id BIGINT NOT NULL, bittrex_order_id VARCHAR(255) NOT NULL, PRIMARY KEY(id, order_id, bittrex_order_id))');
        $this->addSql('COMMENT ON COLUMN local_to_bittrex_exchange_order.id IS \'(DC2Type:localToBittrexExchangeOrderId)\'');
        $this->addSql('COMMENT ON COLUMN local_to_bittrex_exchange_order.order_id IS \'(DC2Type:orderId)\'');
        $this->addSql('COMMENT ON COLUMN local_to_bittrex_exchange_order.bittrex_order_id IS \'(DC2Type:bittrexOrderId)\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE local_to_bittrex_exchange_order');
    }
}
