<?php
namespace SampleClient\SampleProject\SampleModule;

use Dachi\Core\Database;
use Doctrine\ORM\EntityRepository;

class RepositorySample extends EntityRepository {

	public function getRecentSamples($amount = 10) {
		$query = Database::createQuery("SELECT s FROM SampleModule:ModelSample s ORDER BY s.created DESC");
		$query->setMaxResults($amount);
		return $query->getResult();
	}

}