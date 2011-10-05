<?php

class Session
{

    protected $users = array ();

    protected $lang = "en";

    private $users_file;

    // instance de la classe
    private static $instance;

    // Un constructeur privé ; empêche la création directe d'objet
    private function __construct()
    {
        session_start();

        $_SESSION['error'] = "";

        $this->users_file = 'users/users.txt';

        $this->users = $this->_fileToArray($this->users_file);

        //var_dump($this->users);
    }

    public static function getInstance()
    {
        if (! isset (self::$instance))
        {
            $c = __CLASS__ ;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    // Empecher le clonage de l'instance
    public function __clone()
    {
        trigger_error('No clonning allowed!', E_USER_ERROR);
    }

    //
    public function login($login, $password)
    {
        if (in_array($login, array_keys($this->users)))
        {
            if (md5($password) == $this->users[$login])
            {
                $this->_set( array ('login'=>$login, 'last_access'=>$this->lastAccess($login)));
                //var_dump($this->get('login'));
                return true;
            }
        }

        return false;
    }

    public function logout()
    {
        $this->_unset( array ('login'));
    }

    //
    public function ok()
    {
        return $this->get('login');
    }

    //
    public function get($str)
    {
        return isset ($_SESSION[$str])?$_SESSION[$str]:false;
    }

    //
    public function _set($array)
    {
        foreach ($array as $k=>$v)
        {
            $_SESSION[$k] = $v;
        }
    }

    //
    public function _unset($array)
    {
        foreach ($array as $k)
        {
            unset ($_SESSION[$k]);
        }
    }

    public function lang($str = "")
    {
        if ($str == "")
        {
            return $this->lang;
        }
        else $this->lang = $str;
    }

    public function lastAccess($str)
    {
        return file_exists('users/'.$str.'.json')?fileatime('users/'.$str.'.json'):
            false;
        }

        //
        public function save($data)
        {
            return file_put_contents('users/'.$this->get('login').'.json', $data);
        }

        //
        public function restore($all = false)
        {
            if ($all)
            {
                $content = array ();
                foreach (glob("users/*.json") as $file)
                {
                    $tmp = (array)json_decode(file_get_contents($file));
                    if (is_array($tmp) && ! empty($tmp))$content[] = $tmp;
                }
                return empty($content)?false:json_encode($content);
            }
            else
            {
                $filename = $this->get('login');
                return file_exists('users/'.$filename.'.json')
                ?file_get_contents('users/'.$filename.'.json')
                :
                    false;
                }
            }

            //
            public function _fileToArray($filepath)
            {
                $f = file($filepath);
				if ( empty($f) ) exit("[ERROR] There are no registered users!");

                $d = array ();
                foreach ($f as $line_num=>$line)
                {
                    if ($line != "")
                    {
                        $tmp_arr = split(':', $line);
                        /**
                         * [0] : login
                         * [1] : profile
                         * [2] : password
                         * [3] : extra
                         */
                        $d[$tmp_arr[0]] = $tmp_arr[2];
                    }
                }

                return $d;
            }

        }

        $session = Session::getInstance();

?>
