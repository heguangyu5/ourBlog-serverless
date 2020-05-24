<?php

class OurBlog_Auth
{
    const SALT = 'EYFXOEII/T3Y/75D0pUXbz5bqxVIpo7qMipQ7MtnPaUHIvX1nDKgU6KfLf9JpYAvjO7dacpgt8C/';

    protected $email;
    protected $password;

    public function __construct($email, $password)
    {
        // email
        $len = strlen($email);
        if ($len < 5 || $len > 200) {
            throw new InvalidArgumentException('invalid email, length limit 5~200');
        }
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new InvalidArgumentException('email format wrong');
        }
        // password
        $len = strlen($password);
        if ($len < 6 || $len > 50) {
            throw new InvalidArgumentException('invalid password, length limit 6~50');
        }

        $this->email    = $email;
        $this->password = $password;
    }

    public function authenticate()
    {/*
        $uid = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchOne(
            'SELECT uid FROM user WHERE email = ? AND password = ?',
            array($this->email, md5(self::SALT . '-' . $this->password))
        );
*/
        $uid = 0;
        if ($this->email == 'heguangyu5@qq.com' && $this->password == '123456') {
            $uid = 1;
        }
        if ($uid) {
            return array(
                'uid' => $uid,
                'ost' => md5(self::SALT . '-OST-' . $uid)
            );
        }
    }

    public static function isValidOST($ost, $uid)
    {
        return md5(self::SALT . '-OST-' . $uid) == $ost;
    }
}
