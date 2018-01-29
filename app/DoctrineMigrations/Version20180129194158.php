<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180129194158 extends AbstractMigration
{
    public function up(Schema $schema)
    {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('CREATE SEQUENCE user_id_seq');
		$this->addSql('CREATE SEQUENCE bot_id_seq');
		$this->addSql('CREATE SEQUENCE bot_exchange_account_transaction_id_seq');
		$this->addSql('CREATE SEQUENCE bot_trading_session_id_seq');
		$this->addSql('CREATE SEQUENCE bot_trading_session_account_transaction_id_seq');
		$this->addSql('CREATE SEQUENCE order_id_seq');
		$this->addSql('CREATE SEQUENCE user_exchange_account_transaction_id_seq');

    }

    public function down(Schema $schema)
    {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

		$this->addSql('DROP SEQUENCE user_id_seq');
		$this->addSql('DROP SEQUENCE bot_id_seq');
		$this->addSql('DROP SEQUENCE bot_exchange_account_transaction_id_seq');
		$this->addSql('DROP SEQUENCE bot_trading_session_id_seq');
		$this->addSql('DROP SEQUENCE bot_trading_session_account_transaction_id_seq');
		$this->addSql('DROP SEQUENCE order_id_seq');
		$this->addSql('DROP SEQUENCE user_exchange_account_transaction_id_seq');
    }
}
