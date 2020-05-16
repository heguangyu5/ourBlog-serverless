<?php
/**
 * @group post
 */
class OurBlog_Post_EditExternalPostTest extends OurBlog_DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/fixtures.php');
    }

    public function testEditExternalPost()
    {
        $data = array(
            'id'       => 1,
            'category' => '3',
            'title'    => '记录一次Mysql死锁排查过程 - edit',
            'content'  => 'http://www.kissyu.org/2017/02/19/%E8%AE%B0%E5%BD%95%E4%B8%80%E6%AC%A1Mysql%E6%AD%BB%E9%94%81%E6%8E%92%E6%9F%A5%E8%BF%87%E7%A8%8B',
            'tags'     => 'MySQL,死锁'
        );

        $post = new OurBlog_Post(1);
        $post->edit($data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));
        $filterDataSet = new PHPUnit_DbUnit_DataSet_FilterDataSet($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
