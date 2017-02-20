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
            'posts'    => array(),
            'tag'      => array(
                array('id' => 1, 'name' => 'PHP'),
                array('id' => 2, 'name' => 'Linux'),
                array('id' => 3, 'name' => 'MySQL')
            ),
            'post_tag' => array()
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

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key tags
     */
    public function testTagsCannotMissing()
    {
        unset($this->data['tags']);
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

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage tags too long
     */
    public function testTagsMaxLengthIs600()
    {
        $this->data['tags'] = str_repeat('123456789,', 60) . ',';
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage too many tags
     */
    public function testTagsMaxLimitIs10()
    {
        $this->data['tags'] = 'tag1,tag2,tag3,tag4,tag5,tag6,tag7,tag8,tag9,tag10,tag11';
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage tag too long
     */
    public function testTagMaxLenthIs50()
    {
        $this->data['tags'] = str_pad('a', 51, 'a');
        self::$post->add($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage external post url too long
     */
    public function testExtenalPostLinkMaxLengthIs1000()
    {
        $this->data['externalPost'] = 1;
        $this->data['content'] = 'http://www.ourats.com/post/details/?id=133&pad='
                                 . str_pad('a', 1001 - strlen('http://www.ourats.com/post/details/?id=133&pad='), 'a');
        self::$post->add($this->data);
    }

    public function invalidExternalPostUrl()
    {
        return array(
            array('xxx'),
            array('www.ourats.com'),
            array('http://www.ourats.com/?key="a"'),
            array('https://www.baidu.com/?q="bbb"')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid external post url
     * @dataProvider invalidExternalPostUrl
     */
    public function testExtenalPostLinkShouldBeHttpOrHttps($url)
    {
        $this->data['externalPost'] = 1;
        $this->data['content'] = $url;

        self::$post->add($this->data);
    }

    public function testAddPostWithoutTag()
    {
        self::$post->add($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects-without-tag.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('create_date', 'update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
        $this->assertTableEmpty('post_tag');
    }

    public function testAddPostWithAllNewTags()
    {
        $this->data['tags'] = 'tagA,,tagB,tagC,tagC';
        self::$post->add($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects-with-all-new-tags.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('create_date', 'update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testAddPostWithSomeNewTags()
    {
        $this->data['tags'] = 'MySQL,Apache,PHP,Javascript';
        self::$post->add($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects-with-some-new-tags.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('create_date', 'update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testAddExternalPost()
    {
        $data = array(
            'category'     => '2',
            'title'        => '记录一次Mysql死锁排查过程',
            'content'      => 'http://www.kissyu.org/2017/02/19/%E8%AE%B0%E5%BD%95%E4%B8%80%E6%AC%A1Mysql%E6%AD%BB%E9%94%81%E6%8E%92%E6%9F%A5%E8%BF%87%E7%A8%8B/?hmsr=toutiao.io&utm_medium=toutiao.io&utm_source=toutiao.io',
            'externalPost' => '1',
            'tags'         => 'MySQL,死锁'
        );

        self::$post->add($data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects-external-post.php');

        $dataSet = $this->getConnection()->createDataSet(array('posts', 'tag', 'post_tag'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('posts', array('create_date', 'update_date'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
