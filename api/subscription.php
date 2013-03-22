<?php

namespace Api {

    use Lib;

    class Subscription extends \Lib\Dal {
    
        /**
         * ID of user
         */
        public $userId;
        
        /**
         * ID of feed subscribed to
         */
        public $feedId;
        
        /**
         * Returns an array of feeds that the user is subscribed to
         */
        public static function getSubscribedFeeds() {
            $retVal = null;
            
            // Get the user's ID from session
            $user = User::getCurrentUser();
            if ($user) {
                
                $query = 'SELECT f.* FROM subscriptions s INNER JOIN feeds f ON f.feed_id = s.feed_id WHERE s.user_id = :userId';
                $result = Lib\Db::Query($query, [ ':userId' => $user->id ]);
                if ($result && $result->count > 0) {
                    $retVal = [];
                    while ($row = Lib\Db::Fetch($result)) {
                        $retVal[] = new Feed($row);
                    }
                }
                
            }
        
            return $retVal;
        }
    
        /**
         * Returns the items not read since the last visit
         */
        public static function getUnreadItems() {
            $retVal = null;
            
            $user = User::getCurrentUser();
            if ($user) {
                
                $query = 'SELECT * FROM items WHERE item_date > :lastRead AND feed_id IN (SELECT feed_id FROM subscriptions WHERE user_id = :userId) ORDER BY item_date';
                $result = Lib\Db::Query($query, [ ':lastRead' => $user->lastRead, ':userId' => $user->id ]);
                if ($result && $result->count > 0) {
                    $retVal = [];
                    while ($row = Lib\Db::Fetch($result)) {
                        $retVal[] = new Item($row);
                    }
                }
                
            }
            
            return $retVal;
        }
    
    }

}