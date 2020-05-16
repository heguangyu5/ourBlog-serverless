<?php
/**
 * @group auth
 */
class OurBlog_AuthTest extends OurBlog_DatabaseTestCase
{
    public function getDataSet()
    {
        return $this->createArrayDataSet(array());
    }

    public function testEmailMinLenthIs5()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid email, length limit 5~200');

        new OurBlog_Auth('a@bb', '');
    }

    public function testEmailMaxLenthIs200()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid email, length limit 5~200');

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
     * @dataProvider invalidEmailFormats
     */
    public function testEmailFormat($email)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('email format wrong');

        new OurBlog_Auth($email, '');
    }

    public function testPasswordMinLenthIs6()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid password, length limit 6~50');

        new OurBlog_Auth('heguangyu5@qq.com', '12345');
    }

    public function testPasswordMaxLenthIs50()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid password, length limit 6~50');

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
