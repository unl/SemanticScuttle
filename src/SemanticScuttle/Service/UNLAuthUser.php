<?php

require_once 'Auth.php';
require_once 'SemanticScuttle/Service/User.php';

/**
 * SemanticScuttle extended user management service utilizing
 * the UNL_Auth package to enable authentication against
 * different services, i.e. CAS or other databases.
 *
 * Requires the Log packages for debugging purposes.
 */
class SemanticScuttle_Service_UNLAuthUser extends SemanticScuttle_Service_User
{
    /**
     * UNL_Auth instance
     *
     * @var UNL_Auth
     */
    protected $auth = null;

    /**
     * If we want to debug authentication process
     *
     * @var boolean
     */
    protected $authdebug = false;

    /**
    * Authentication type (i.e. CAS, SimpleCAS)
    *
    * @var string
    */
    var $authtype = null;

    /**
    * Authentication options
    *
    * @var array
    */
    var $authoptions = null;



    /**
     * Returns the single service instance
     *
     * @param sql_db $db Database object
     *
     * @return SemanticScuttle_Service_UNLAuthUser
     */
    public static function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new self($db);
        }
        return $instance;
    }



    /**
     * Create new instance
     *
     * @var sql_db $db Database object
     */
    protected function __construct($db)
    {
        parent::__construct($db);

        $this->authtype    = $GLOBALS['authType'];
        $this->authoptions = $GLOBALS['authOptions'];
        $this->authdebug   = $GLOBALS['authDebug'];

        //FIXME: throw error when no authtype set?
        if (!$this->authtype) {
            return;
        }
        require_once 'UNL/Auth.php';
        require_once 'HTTP/Request2.php';
        $this->auth = UNL_Auth::factory($this->authtype, $this->authoptions);
        //FIXME: check if it worked (i.e. db connection)
        if ($this->authdebug) {
            require_once 'Log.php';
            $this->auth->logger = Log::singleton(
                'display', '', '', array(), PEAR_LOG_DEBUG
            );
            $this->auth->enableLogging = true;
        }
        $this->auth->setShowLogin(false);
    }



    /**
     * Return current user id based on session or cookie
     *
     * @return mixed Integer user id or boolean false when user
     *               could not be found or is not logged on.
     */
    public function getCurrentUserId()
    {
        if (!$this->auth) {
            return parent::getCurrentUserId();
        }

        //FIXME: caching?
        $name = $this->auth->getUser();
        if (!$name) {
            return parent::getCurrentUserId();
        }
        return $this->getIdFromUser($name);
    }



    /**
     * Try to authenticate and login a user
     *
     * @return boolean True if the user could be authenticated,
     *                 false if not.
     */
    public function login()
    {
        $ok = $this->loginAuth();
        if (!$ok) {
            return false;
        }

        //utilize real login method to get longtime cookie support etc.
        $ok = parent::login($this->auth->getUser(), '', false);
        if ($ok) {
            return $ok;
        }
    }



    /**
    * Authenticate the user
    *
    * @return boolean If the user has been successfully authenticated or not
    */
    public function loginAuth()
    {
        $this->auth->login();

        if (!$this->auth->isLoggedIn()) {
            return false;
        }

        $uid = $this->auth->getUser();
        //put user in database
        if (!$this->getUserByUsername($uid)) {
            $this->addUser(
                $uid, '',
                $uid . $GLOBALS['authEmailSuffix']
            );
        }

        return true;
     }



    /**
     * Logs the current user out of the system.
     *
     * @return void
     */
    public function logout()
    {
        parent::logout();

        if ($this->auth) {
            $this->auth->logout();
            $this->auth = null;
        }
    }

}
