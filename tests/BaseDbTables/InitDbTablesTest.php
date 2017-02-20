<?php
/**
 * 这其实不是一个测试,只是用来初始化内容相对不变的数据库表的
 *
 * @group InitDbTables
 */
class OurBlog_BaseDbTables_InitTest extends OurBlog_DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createArrayDataSet(array(
            'category' => include __DIR__ . '/tables/category.php',
            'user'     => include __DIR__ . '/tables/user.php'
        ));
    }

    public function testNothing()
    {}
}
