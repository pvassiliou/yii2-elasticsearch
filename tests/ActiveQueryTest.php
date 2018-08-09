<?php

namespace yiiunit\extensions\elasticsearch;
use yiiunit\extensions\elasticsearch\data\ar\Item;


/**
 * @group elasticsearch
 */
class ActiveQueryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $command = $this->getConnection()->createCommand();

        // delete index
        if ($command->indexExists(Item::index())) {
            $command->deleteIndex(Item::index());
        }
        Item::setUpMapping($command);

        $command->insert(Item::index(), Item::type(), ['name' => 'item1', 'category_id' => 'category1'], 1);

        $command->flushIndex();
    }

    /**
     * @throws \yii\elasticsearch\Exception
     */
    public function testColumn()
    {
        $activeQuery = Item::find()->where(['name' => 'item1'])->asArray();

        $result = $activeQuery->column('category_id', $this->getConnection());
        $this->assertEquals(['category1'], $result);
        $result = $activeQuery->column('_id', $this->getConnection());
        $this->assertEquals([1], $result);
        $result = $activeQuery->column('noname', $this->getConnection());
        $this->assertEquals([null], $result);
        $result = $activeQuery->scalar('name', $this->getConnection());
        $this->assertEquals('item1', $result);
    }
}