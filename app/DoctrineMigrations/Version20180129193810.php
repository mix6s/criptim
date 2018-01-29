<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180129193810 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, domain_user_id BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C05FB297 ON users (confirmation_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E945B9CA8D ON users (domain_user_id)');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN users.domain_user_id IS \'(DC2Type:userId)\'');
        $this->addSql('CREATE TABLE domain_user (id BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN domain_user.id IS \'(DC2Type:userId)\'');
        $this->addSql('CREATE TABLE bot (id BIGINT NOT NULL, exchange_id VARCHAR(255) NOT NULL, trading_strategy_id VARCHAR(255) NOT NULL, trading_strategy_settings JSON NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN bot.id IS \'(DC2Type:botId)\'');
        $this->addSql('COMMENT ON COLUMN bot.exchange_id IS \'(DC2Type:exchangeId)\'');
        $this->addSql('COMMENT ON COLUMN bot.trading_strategy_id IS \'(DC2Type:tradingStrategyId)\'');
        $this->addSql('COMMENT ON COLUMN bot.trading_strategy_settings IS \'(DC2Type:tradingStrategySettings)\'');
        $this->addSql('CREATE TABLE bot_exchange_account (bot_id BIGINT NOT NULL, exchange_id VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, balance JSONB NOT NULL, PRIMARY KEY(bot_id, exchange_id, currency))');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account.bot_id IS \'(DC2Type:botId)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account.exchange_id IS \'(DC2Type:exchangeId)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account.balance IS \'(DC2Type:money)\'');
        $this->addSql('CREATE TABLE bot_exchange_account_transaction (id BIGINT NOT NULL, bot_id BIGINT NOT NULL, exchange_id VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, money JSONB NOT NULL, balance JSONB NOT NULL, type VARCHAR(255) NOT NULL, dt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account_transaction.id IS \'(DC2Type:botExchangeAccountTransactionId)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account_transaction.bot_id IS \'(DC2Type:botId)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account_transaction.exchange_id IS \'(DC2Type:exchangeId)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account_transaction.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account_transaction.money IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account_transaction.balance IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN bot_exchange_account_transaction.dt IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('CREATE TABLE bot_trading_session (id BIGINT NOT NULL, bot_id BIGINT NOT NULL, exchange_id VARCHAR(255) NOT NULL, trading_strategy_id VARCHAR(255) NOT NULL, trading_strategy_settings JSON NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.id IS \'(DC2Type:botTradingSessionId)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.bot_id IS \'(DC2Type:botId)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.exchange_id IS \'(DC2Type:exchangeId)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.trading_strategy_id IS \'(DC2Type:tradingStrategyId)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.trading_strategy_settings IS \'(DC2Type:tradingStrategySettings)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.created_at IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.updated_at IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session.ended_at IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('CREATE TABLE bot_trading_session_account (bot_trading_session_id BIGINT NOT NULL, currency VARCHAR(255) NOT NULL, balance JSONB NOT NULL, PRIMARY KEY(bot_trading_session_id, currency))');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account.bot_trading_session_id IS \'(DC2Type:botTradingSessionId)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account.balance IS \'(DC2Type:money)\'');
        $this->addSql('CREATE TABLE bot_trading_session_account_transaction (id BIGINT NOT NULL, bot_trading_session_id BIGINT NOT NULL, currency VARCHAR(255) NOT NULL, money JSONB NOT NULL, balance JSONB NOT NULL, type VARCHAR(255) NOT NULL, dt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account_transaction.id IS \'(DC2Type:botTradingSessionAccountTransactionId)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account_transaction.bot_trading_session_id IS \'(DC2Type:botTradingSessionId)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account_transaction.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account_transaction.money IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account_transaction.balance IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN bot_trading_session_account_transaction.dt IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('CREATE TABLE orders (id BIGINT NOT NULL, bot_trading_session_id BIGINT NOT NULL, type VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, exec_amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, symbol JSONB NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN orders.id IS \'(DC2Type:orderId)\'');
        $this->addSql('COMMENT ON COLUMN orders.bot_trading_session_id IS \'(DC2Type:botTradingSessionId)\'');
        $this->addSql('COMMENT ON COLUMN orders.symbol IS \'(DC2Type:currencyPair)\'');
        $this->addSql('COMMENT ON COLUMN orders.created_at IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('COMMENT ON COLUMN orders.updated_at IS \'(DC2Type:dateTimeImmutable)\'');
        $this->addSql('CREATE TABLE user_exchange_account (user_id BIGINT NOT NULL, exchange_id VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, balance JSONB NOT NULL, PRIMARY KEY(user_id, exchange_id, currency))');
        $this->addSql('COMMENT ON COLUMN user_exchange_account.user_id IS \'(DC2Type:userId)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account.exchange_id IS \'(DC2Type:exchangeId)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account.balance IS \'(DC2Type:money)\'');
        $this->addSql('CREATE TABLE user_exchange_account_transaction (id BIGINT NOT NULL, user_id BIGINT NOT NULL, exchange_id VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, money JSONB NOT NULL, balance JSONB NOT NULL, type VARCHAR(255) NOT NULL, dt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN user_exchange_account_transaction.id IS \'(DC2Type:userExchangeAccountTransactionId)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account_transaction.user_id IS \'(DC2Type:userId)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account_transaction.exchange_id IS \'(DC2Type:exchangeId)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account_transaction.currency IS \'(DC2Type:currency)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account_transaction.money IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account_transaction.balance IS \'(DC2Type:money)\'');
        $this->addSql('COMMENT ON COLUMN user_exchange_account_transaction.dt IS \'(DC2Type:dateTimeImmutable)\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE domain_user');
        $this->addSql('DROP TABLE bot');
        $this->addSql('DROP TABLE bot_exchange_account');
        $this->addSql('DROP TABLE bot_exchange_account_transaction');
        $this->addSql('DROP TABLE bot_trading_session');
        $this->addSql('DROP TABLE bot_trading_session_account');
        $this->addSql('DROP TABLE bot_trading_session_account_transaction');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE user_exchange_account');
        $this->addSql('DROP TABLE user_exchange_account_transaction');
    }
}
