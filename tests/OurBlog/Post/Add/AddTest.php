<?php
/**
 * @group post
 */
class OurBlog_Post_AddTest extends OurBlog_DatabaseTestCase
{
    protected $data;
    protected static $post;

    public function getDataSet()
    {
        $this->data = include __DIR__ . '/data.php';
        if (!self::$post) {
            self::$post = new OurBlog_Post(OurBlog_DatabaseTestCase::getDb(), 1);
        }

        return $this->createArrayDataSet(array(
            'posts' => array()
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key category
     */
    public function testCategoryCannotMissing()
    {
        unset($this->data['category']);
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage category required
     */
    public function testCategoryIsRequried()
    {
        $this->data['category'] = '';
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key title
     */
    public function testTitleCannotMissing()
    {
        unset($this->data['title']);
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage title required
     */
    public function testTitleIsRequried()
    {
        $this->data['title'] = '';
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key content
     */
    public function testContentCannotMissing()
    {
        unset($this->data['content']);
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage content required
     */
    public function testContentIsRequried()
    {
        $this->data['content'] = '';
        self::$post->add($this->data);
    }

    public function invalidCategoryIds()
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
     * @expectedExceptionMessage invalid category
     * @dataProvider invalidCategoryIds
     */
    public function testCategoryShouldBeDBAIPK($category)
    {
        $this->data['category'] = $category;
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage title maxlength is 500
     */
    public function testTitleMaxLengthIs500()
    {
        $this->data['title'] = $this->data['title']
                               . str_pad('a', 501 - mb_strlen($this->data['title'], 'UTF-8'), 'a');
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage content maxlength is 64000
     */
    public function testContentMaxLengthIs64000()
    {
        $this->data['content'] = $this->data['content']
                               . str_pad('a', 64001 - strlen($this->data['content']), 'a');
        self::$post->add($this->data);
    }

    public function testAddPost()
    {
        self::$post->add($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('create_date', 'update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
