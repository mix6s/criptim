<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180305122031 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE user_account_transaction (id BIGINT NOT NULL, user_id BIGINT NOT NULL, currency VARCHAR(255) NOT NULL, money JSONB NOT NULL, balance JSONB NOT NULL, type VARCHAR(255) NOT NULL, dt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN user_account_transaction.id IS \'(DC2Type:userAccountTransactionId)\'');
        $this->addSql('COMMENT ON COLUMN user_account_transaction.user_id IS \'(DC2Type:userId)\'');
        $this->addSql('COMMENT ON COLUMN user_account_transaction.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN user_account_transaction.money IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN user_account_transaction.balance IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN user_account_transaction.dt IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('CREATE TABLE user_account (user_id BIGINT NOT NULL, currency VARCHAR(255) NOT NULL, balance JSONB NOT NULL, PRIMARY KEY(user_id, currency))');
        $this->addSql('COMMENT ON COLUMN user_account.user_id IS \'(DC2Type:userId)\'');
        $this->addSql('COMMENT ON COLUMN user_account.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN user_account.balance IS \'(DC2Type:money)\'');

		$this->addSql('CREATE SEQUENCE user_account_transaction_id_seq');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE user_account_transaction_id_seq');
        $this->addSql('DROP TABLE user_account_transaction');
        $this->addSql('DROP TABLE user_account');
    }
}
