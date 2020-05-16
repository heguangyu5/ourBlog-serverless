<?php
/**
 * @group post
 */
class OurBlog_Post_DeleteTest extends OurBlog_DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/fixtures.php');
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

        $post = new OurBlog_Post(1);
        $post->delete($id);
    }

    public function testDeletePost()
    {
        $post = new OurBlog_Post(1);
        $post->delete(1);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects.php');
        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));

        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testUserCannotDeleteOthersPost()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('you can only delete your own post');

        $post = new OurBlog_Post(1);
        $post->delete(2);
    }
}
