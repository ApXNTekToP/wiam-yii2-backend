<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%requests}}`.
 */
class m260205_020327_create_requests_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = \'requests_status\') THEN
                CREATE TYPE requests_status AS ENUM (\'new\', \'processing\', \'approved\', \'declined\');
            END IF;
        END $$;
        ');

        $this->createTable('{{%requests}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull(),
            'term' => $this->integer()->notNull(),
            'status' => 'requests_status NOT NULL DEFAULT \'new\'',
        ]);

        $this->createIndex('{{%idx_requests_user_id}}', '{{%requests}}', 'user_id');
        $this->createIndex('{{%idx_requests_status}}', '{{%requests}}', 'status');
        $this->execute('
            CREATE UNIQUE INDEX IF NOT EXISTS idx_requests_user_approved 
                ON {{%requests}} (user_id) WHERE status = \'approved\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx_requests_user_id}}', '{{%requests}}');
        $this->dropIndex('{{%idx_requests_status}}', '{{%requests}}');
        $this->execute('DROP INDEX IF EXISTS idx_requests_user_approved;');

        $this->dropTable('{{%requests}}');

        $this->execute('DROP TYPE IF EXISTS requests_status;');
    }
}
