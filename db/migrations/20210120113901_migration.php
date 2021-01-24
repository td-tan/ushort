<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Migration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // create the user table
        $table = $this->table('users');
        $table->addColumn('email', 'string', array('limit' => 100, 'null' => false))
              ->addColumn('password', 'string', array('null' => false))
              ->addColumn('admin', 'boolean', array('default' => false, 'null' => false))
              ->addIndex(array('email'), array('unique' => true))
              ->addTimestamps()
              ->create();

        // inserting multiple rows in users table
        $rows = [
            [
              'id'    => 1,
              'email'  => 'admin@ushort.example',
              'password' => password_hash('admin', PASSWORD_DEFAULT),
              'admin' => True
            ],
            [
              'id'    => 2,
              'email'  => 'test@ushort.example',
              'password' => password_hash('test', PASSWORD_DEFAULT),
            ]
        ];
        if ($this->isMigratingUp()) {
          $table->insert($rows)->update();
        }
        // create the links table
        $table = $this->table('links');
        $table->addColumn('user_id', 'integer')
              ->addColumn('link', 'string', array('null' => false))
              ->addColumn('short', 'string', array('null' => false)) // short link
              ->addColumn('deleted', 'boolean', array('null' => false, 'default' => false)) // for soft delete
              ->addIndex(array('user_id', 'short'), array('unique' => true))
              ->addForeignKey('user_id', 'users')
              ->addTimestamps()
              ->create();

        // create the token table
        $table = $this->table('tokens');
        $table->addColumn('user_id', 'integer')
              ->addColumn('ip_addr', 'string', array('null' => false))
              ->addColumn('refresh_token', 'string', array('null' => false))
              ->addColumn('expire_at', 'timestamp', array('null' => false))
              ->addIndex(array('user_id', 'ip_addr', 'refresh_token'), array('unique' => true))
              ->addForeignKey('user_id', 'users', ['id'], ['delete' => 'CASCADE'])
              ->addTimestamps()
              ->create();
    }
}
