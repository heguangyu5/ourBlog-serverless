<?php

class OurBlog_Auth
{
    const SALT = 'EYFXOEII/T3Y/75D0pUXbz5bqxVIpo7qMipQ7MtnPaUHIvX1nDKgU6KfLf9JpYAvjO7dacpgt8C/';

    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function authenticate($email, $password)
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

        $password = md5(self::SALT . '-' . $password);

        $stmt = $this->db->prepare('SELECT uid FROM user WHERE email = ? AND password = ?');
        $stmt->execute(array($email, $password));
        return $stmt->fetchColumn();
    }
}
