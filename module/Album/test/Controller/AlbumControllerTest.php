<?php
namespace AlbumTest\Controller;

use Album\Controller\AlbumController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AlbumControllerTest extends AbstractHttpControllerTestCase
{
	protected $traceError = true;
	
	public function setUp()
	{
		$configOverrides = [];
		$this->setApplicationConfig(ArrayUtils::merge(
			include __DIR__ . '/../../../../config/application.config.php',
			$configOverrides
		));
		parent::setUp();
		
		$services = $this->getApplicationServiceLocator();
		$config = $services->get('config');
		unset($config['db']);
		$services->setAllowOverride(true);
		$services->setService('config', $config);
		$services->setAllowOverride(false);
	}
	
	public function testindexActionCanBeAccessed()
	{
		$this->dispatch('/album');
		$this->assertResponseStatusCode(200);
		$this->assertModuleName('Album');
		$this->assertControllerName(AlbumController::class);
		$this->assertControllerClass('AlbumController');
		$this->assertMatchedRouteName('album');
	}
}
