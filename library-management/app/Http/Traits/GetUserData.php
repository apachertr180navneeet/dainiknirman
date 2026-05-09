<?php

namespace App\Http\Traits;

trait GetUserData
{
    /**
     * Get user's all data.
     *
     * @param  object   $user
     * @param  boolean  $withId
     * @return object
     */
    private function getUserAllData($user, $withId = false)
    {
        // Get user categories
        $user->load([]);
        //--------------------

        // Hide id
        if (!$withId) {
            // $user->makeHidden('id');
        }
        //--------

        return $user;
    }
}
