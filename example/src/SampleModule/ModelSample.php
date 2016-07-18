<?php
namespace SampleClient\SampleProject\SampleModule;

use Dachi\Core\Model;

/**
 * @Entity(repositoryClass="RepositorySample")
 * @Table(name="samples")
 */
class ModelSample extends Model {
	/**
	 * @Id @Column(type="integer") @GeneratedValue
	 **/
	protected $id;

	/**
	 * @Column(type="string")
	 **/
	protected $name;

    /**
     * @Column(type="datetime")
     **/
    protected $created;

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getCreated() {
		return $this->created;
	}

	public function setCreated($datetime) {
		$this->created = $datetime;
	}
}