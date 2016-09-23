<?php


namespace AppBundle\Dropbox;


use Dropbox\ValueStore;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionValueStore implements ValueStore
{
    const TOKEN_NAME = 'dropbox-auth-token';
    
    /** @var Session */
    private $session;

    /**
     * DropboxValueStore constructor.
     * @param $session
     */
    public function __construct($session)
    {
        $this->session = $session;
    }


    function get()
    {
        return $this->session->get(self::TOKEN_NAME);
    }

    function set($value)
    {
        $this->session->set(self::TOKEN_NAME, $value);
    }

    function clear()
    {
        $this->session->remove(self::TOKEN_NAME);
    }
}