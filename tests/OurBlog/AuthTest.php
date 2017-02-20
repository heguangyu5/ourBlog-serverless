<?php
/**
 * @group auth
 */
class OurBlog_AuthTest extends PHPUnit_Framework_TestCase
{
    protected static $auth;

    public function setUp()
    {
        if (!self::$auth) {
            self::$auth = new OurBlog_Auth(OurBlog_DatabaseTestCase::getDb());
        }
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid email, length limit 5~200
     */
    public function testEmailMinLenthIs5()
    {
        self::$auth->authenticate('a@bb', '');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid email, length limit 5~200
     */
    public function testEmailMaxLenthIs200()
    {
        self::$auth->authenticate(
            str_pad('a@b.com', 201, 'a', STR_PAD_LEFT),
            ''
        );
    }

    public function invalidEmailFormats()
    {
        return array(
            array('aaaaaaaa'),
            array('<script>alert(1)</script>@qq.com'),
            array('1111@####'),
            array('a@bcom')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage email format wrong
     * @dataProvider invalidEmailFormats
     */
    public function testEmailFormat($email)
    {
        self::$auth->authenticate($email, '');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid password, length limit 6~50
     */
    public function testPasswordMinLenthIs6()
    {
        self::$auth->authenticate('heguangyu5@qq.com', '12345');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid password, length limit 6~50
     */
    public function testPasswordMaxLenthIs50()
    {
        self::$auth->authenticate('heguangyu5@qq.com', str_pad('12345', 51, '6'));
    }

    public function testAuthenticateWillReturnUIDIfEmailPasswordOK()
    {
        $this->assertEquals(1, self::$auth->authenticate('heguangyu5@qq.com', '123456'));
    }

    public function wrongEmailPasswords()
    {
        return array(
            array('heguangyu@qq.com', '1234567'),
            array('heguangyu5@qq.com', '1234567'),
            array('heguangyu@qq.com', '123456')
        );
    }

    /**
     * @dataProvider wrongEmailPasswords
     */
    public function testAuthenticateWillReturnFalseIfEmailPasswordNotMatch($email, $password)
    {
        $this->assertEquals(false, self::$auth->authenticate($email, $password));
    }
}
