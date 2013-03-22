<?php

namespace Api {

    use stdClass;
    use Lib;

    class User extends \Lib\Dal {
    
        /**
         * Database mappings
         */
        protected $_dbTable = 'users';
        protected $_dbMap = [
            'id' => 'user_id',
            'facebookId' => 'user_fbid',
            'handle' => 'user_handle',
            'firstName' => 'user_fname',
            'lastName' => 'user_lname',
            'lastRead' => 'user_lastread'
        ];
        protected $_dbPrimaryKey = 'id';
        
        /**
         * User ID
         */
        public $id;
    
        /**
         * User's Facebook ID
         */
        public $facebookId;
        
        /**
         * User's Facebook handle
         */
        public $handle;
        
        /**
         * User's first name
         */
        public $firstName;
        
        /**
         * User's last name
         */
        public $lastName;
        
        /**
         * Unix time stamp of the last item read
         */
        public $lastRead;
        
        /**
         * Verifies that a Facebook user is in the database. If not, the record can be created
         * @param $user object Facebook user object
         * @param $create bool Whether to create the user record if it doesn't already exist
         * @return mixed User object if the record exists/is created, null if nothing
         */
        public static function verifyFacebookUser($fbUser, $create) {
        
            $retVal = null;
        
            if (is_array($fbUser) && isset($fbUser['id'])) {
            
                $user = User::getByFilter([ 'facebookId' => $fbUser['id'] ]);
                $user = $user ? $user[0] : null;
                
                if (null !== $user && $user->facebookId == $fbUser['id']) {
                    $retVal = $user;
                } else {
                    
                    $user->facebookId = $fbUser['id'];
                    $user->handle = $fbUser['username'];
                    $user->firstName = $fbUser['first_name'];
                    $user->lastName = $fbUser['last_name'];
                    $user->lastRead = 0;
                    
                    if ($user->sync(true)) {
                        $retVal = $user;
                    }
                    
                }
            
            }
            
            return $retVal;
        
        }
        
        /**
         * Returns the current user session object
         */
        public static function getCurrentUser() {
            return Lib\Url::Get('user', null, $_SESSION);
        }
        
        /**
         * Updates the last read date for a user
         */
        public static function updateLastRead($vars) {
            
            // Fire up the session if needed
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            
            $user = self::getCurrentUser();
            $lastRead = Lib\Url::Get('lastRead', null, $vars);
            
            if ($user && $lastRead) {
                $user->lastRead = $lastRead;
                $user->sync();
            }
            
        }
    
    }

}