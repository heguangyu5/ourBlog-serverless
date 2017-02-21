<?php
/**
 * @group post
 */
class OurBlog_Post_EditTest extends OurBlog_DatabaseTestCase
{
    protected $data;

    public function getDataSet()
    {
        $this->data = include __DIR__ . '/data.php';

        return $this->createArrayDataSet(include __DIR__ . '/fixtures.php');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key id
     */
    public function testIdCannotMissing()
    {
        unset($this->data['id']);

        $post = new OurBlog_Post(1);
        $post->edit($this->data);
    }

    public function invalidPostIds()
    {
        return array(
            array('abc'),
            array('---'),
            array('000'),
            array('-1'),
            array('0xabc')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid id
     * @dataProvider invalidPostIds
     */
    public function testIdShouldBeDBAIPK($id)
    {
        $this->data['id'] = $id;

        $post = new OurBlog_Post(1);
        $post->edit($this->data);
    }

    public function testEditPost()
    {
        $post = new OurBlog_Post(1);
        $post->edit($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage you can only edit your own post
     */
    public function testUserCannotEditOthersPost()
    {
        $post = new OurBlog_Post(2);
        $post->edit($this->data);
    }

    public function testEditPostDeleteAllTags()
    {
        $this->data['tags'] = '';

        $post = new OurBlog_Post(1);
        $post->edit($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects-delete-all-tags.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testEditPostAddExistTags()
    {
        $this->data['tags'] = 'PHP,MySQL,Linux';

        $post = new OurBlog_Post(1);
        $post->edit($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects-add-exist-tags.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
