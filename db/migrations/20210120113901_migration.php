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
        $table->addColumn('email', 'string', array('limit' => 100))
              ->addColumn('password', 'string')
              ->addIndex(array('email'), array('unique' => true))
              ->addTimestamps()
              ->create();

        // inserting multiple rows
        $rows = [
            [
              'id'    => 1,
              'email'  => 'admin@ushort.example',
              'password' => password_hash('admin', PASSWORD_DEFAULT),
            ],
            [
              'id'    => 2,
              'email'  => 'test@ushort.example',
              'password' => password_hash('test', PASSWORD_DEFAULT),
            ]
        ];

        $table->insert($rows)->save();
    }
}
