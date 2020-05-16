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

    public function testIdCannotMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing required key id');

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
     * @dataProvider invalidPostIds
     */
    public function testIdShouldBeDBAIPK($id)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid id');

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
        $filterDataSet = new PHPUnit_DbUnit_DataSet_FilterDataSet($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testUserCannotEditOthersPost()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('you can only edit your own post');

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
        $filterDataSet = new PHPUnit_DbUnit_DataSet_FilterDataSet($dataSet);
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
        $filterDataSet = new PHPUnit_DbUnit_DataSet_FilterDataSet($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
