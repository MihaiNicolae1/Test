<?php

namespace App\Repository;

class UserRepository extends BaseRepository
{
    public function insertByEmail($email){
        $account = $this->find(null, ['email' => $email], null);
        if(!empty($account)) {
            throw new \Exception('Sorry! An user with this email already exists');
        }
    }
}
