<?php
namespace SampleClient\SampleProject\SampleModule\Models;

use Dachi\Core\Model;

/**
 * @Entity(repositoryClass="SampleRepository")
 * @Table(name="samples")
 */
class Sample extends Model {
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
    protected $created_at;

	public static function create() {
		$sample = new Sample();
		$sample->setName("Unnamed");
		$sample->setCreatedAt(new \DateTime());
		return $sample;
	}

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($value) {
		$this->name = $value;
	}

	public function getCreatedAt() {
		return $this->created_at;
	}

	public function setCreatedAt($value) {
		$this->created_at = $value;
	}

	public function asArray($safe = false, $eager = false) {
		$data = array(
			"id"         => $this->getId(),
			"name"       => $this->getName(),
			"created_at" => $this->getCreatedAt()
		);

		return $data;
	}

}
