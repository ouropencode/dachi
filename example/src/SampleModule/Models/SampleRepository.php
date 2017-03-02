<?php

namespace SampleClient\SampleProject\SampleModule\Models;

use Dachi\Core\Database;
use Dachi\Core\Repository;

class SampleRepository extends Repository
{
    public function getRecentSamples($amount = 10)
    {
        $query = Database::createQuery('SELECT s FROM SampleModule:Sample s ORDER BY s.created_at DESC');
        $query->setMaxResults($amount);

        return $query->getResult();
    }
}
