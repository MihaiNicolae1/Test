<?php

namespace App\Repository;

use App\Entity\Company;

class CompanyRepository extends BaseRepository
{
    public function insertByEmail($email){
        $account = $this->find(null, ['email' => $email], null);
        if(!empty($account)) {
            throw new \Exception('Sorry! A company with this email already exists');
        }
    }
}
