<?php
namespace Dachi\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;

class Dachi_TestBase extends TestCase {
	public function setUp(): void
	{
		if(!file_exists(__DIR__ . "/.test-temp"))
			mkdir(__DIR__ . "/.test-temp");

		// create temporary dachi environment
		$tempFolder = __DIR__ . "/.test-temp/test-" . uniqid() . '-' . md5(mt_rand());
		mkdir($tempFolder);
		chdir($tempFolder);
		mkdir("cache");
		$config = array(
			"dachi" => array(
				"timezone" => "Europe/London"
			)
		);
		file_put_contents("cache/dachi.config.ser", serialize(array(
			"production" => $config,
			"development" => $config,
			"local" => $config
		)));

		file_put_contents("cache/dachi.routes.ser", serialize(array(
			"__unit_test" => array(
				"route" => array(
					"class" => "Dachi\\Tests\\FakeRouteProvider",
					"method" => "getResult",
					"route" => "__unit_test",
					"variables" => array()
				),
				"children" => array(
					"*" => array(
						"route" => array(
							"class" => "Dachi\\Tests\\FakeRouteProvider",
							"method" => "getWildcardResult",
							"route" => "__unit_test/:test_variable",
							"variables" => array(array(1, "test_variable"))
						),
						"children" => array(
							"test" => array(
								"route" => array(
									"class" => "Dachi\\Tests\\FakeRouteProvider",
									"method" => "getWildcardChildResult",
									"route" => "__unit_test/:test_variable/test",
									"variables" => array(array(1, "test_variable"))
								)
							)
						)
					),
					"test" => array(
						"route" => array(
							"class" => "Dachi\\Tests\\FakeRouteProvider",
							"method" => "getNonWildcardResult",
							"route" => "__unit_test/test",
							"variables" => array()
						),
						"children" => array(
							"test" => array(
								"route" => array(
									"class" => "Dachi\\Tests\\FakeRouteProvider",
									"method" => "getNonWildcardChildResult",
									"route" => "__unit_test/test/test",
									"variables" => array()
								)
							)
						)
					),
					"chain" => array(
						"route" => array(
							"class" => "Dachi\\Tests\\FakeRouteProvider",
							"method" => "getChainedResultA",
							"route" => "__unit_test/chain",
							"variables" => array()
						),
						"children" => array(
							"test" => array(
								"route" => array(
									"class" => "Dachi\\Tests\\FakeRouteProvider",
									"method" => "getChainedResultB",
									"render-path" => "__unit_test/chain",
									"route" => "__unit_test/chain/result",
									"variables" => array()
								)
							)
						)
					)
				)
			)
		)));

		file_put_contents("cache/dachi.modules.ser", serialize(array(
			"UnitTestModuleA" => array(
				"namespace" => "UnitTestNamespace\\UnitTestModuleA",
				"shortname" => "UnitTestModuleA",
				"path"      => "/tmp/fakepath/a"
			),
			"UnitTestModuleB" => array(
				"namespace" => "UnitTestNamespace\\UnitTestModuleB",
				"shortname" => "UnitTestModuleB",
				"path"      => "/tmp/fakepath/b"
			),
			"UnitTestModuleC" => array(
				"namespace" => "UnitTestNamespace\\UnitTestModuleC",
				"shortname" => "UnitTestModuleC",
				"path"      => "/tmp/fakepath/c"
			)
		)));

		mkdir('views');
		file_put_contents('views/base.twig', "test <radon-block id=\"test_block\"></radon-block> test");
		file_put_contents('views/test.twig', "hello world {{ test }}");

		mkdir("src");
		mkdir("src/UnitTestModuleA");
		$testRepository = <<<'EOT'
<?php
namespace UnitTestNamespace\UnitTestModuleA;

use Dachi\Core\Database;
use Doctrine\ORM\EntityRepository;

class RepositoryTest extends EntityRepository {
}
EOT;
		file_put_contents('src/UnitTestModuleA/RepositoryTest.php', $testRepository);

		$testModel = <<<'EOT'
<?php
namespace UnitTestNamespace\UnitTestModuleA;
use Dachi\Core\Model;
/**
 * @Entity(repositoryClass="RepositoryTest")
 * @Table(name="test")
 */
class ModelTest extends Model {
	/**
	 * @Id @Column(type="integer") @GeneratedValue
	 **/
	protected $id;

	/**
	 * @Column(type="string")
	 **/
	protected $name;

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}
}
EOT;
		file_put_contents('src/UnitTestModuleA/ModelTest.php', $testModel);
	}
}
