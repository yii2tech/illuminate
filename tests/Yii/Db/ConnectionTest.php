<?php

namespace Yii2tech\Illuminate\Test\Yii\DB;

use Yii;
use yii\db\Query;
use Yii2tech\Illuminate\Test\TestCase;
use Illuminate\Database\Schema\Blueprint;

class ConnectionTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
        $this->seedData();
    }

    /**
     * Setup the database schema.
     *
     * @return void
     */
    protected function createSchema()
    {
        $this->getSchemaBuilder()->create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('price')->default(0);
        });
    }

    /**
     * Seeds the database.
     *
     * @return void
     */
    protected function seedData()
    {
        $this->getDbConnection()->table('items')->insert([
            'name' => 'item1',
            'price' => 10,
        ]);
        $this->getDbConnection()->table('items')->insert([
            'name' => 'item2',
            'price' => 20,
        ]);
    }

    public function testOpen()
    {
        Yii::$app->db->open();

        $this->assertSame($this->getDbConnection()->getPdo(), Yii::$app->db->pdo);
    }

    /**
     * @depends testOpen
     */
    public function testQuery()
    {
        $rows = (new Query())->from('items')->all();

        $this->assertCount(2, $rows);
    }
}
