<?php
/**
 * @group auth
 */
class OurBlog_AuthTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid email, length limit 5~200
     */
    public function testEmailMinLenthIs5()
    {
        new OurBlog_Auth('a@bb', '');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid email, length limit 5~200
     */
    public function testEmailMaxLenthIs200()
    {
        new OurBlog_Auth(
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
        new OurBlog_Auth($email, '');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid password, length limit 6~50
     */
    public function testPasswordMinLenthIs6()
    {
        new OurBlog_Auth('heguangyu5@qq.com', '12345');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid password, length limit 6~50
     */
    public function testPasswordMaxLenthIs50()
    {
        new OurBlog_Auth('heguangyu5@qq.com', str_pad('12345', 51, '6'));
    }

    public function testAuthenticateWillReturnUIDIfEmailPasswordOK()
    {
        $auth   = new OurBlog_Auth('heguangyu5@qq.com', '123456');
        $result = $auth->authenticate();

        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(Zend_Auth_Result::SUCCESS, $result->getCode());
        $this->assertEquals(1, $result->getIdentity());
        $this->assertTrue($result->isValid());
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
        $auth   = new OurBlog_Auth($email, $password);
        $result = $auth->authenticate();

        $this->assertInstanceOf('Zend_Auth_Result', $result);
        $this->assertEquals(Zend_Auth_Result::FAILURE, $result->getCode());
        $this->assertEquals(0, $result->getIdentity());
        $this->assertFalse($result->isValid());
    }
}
