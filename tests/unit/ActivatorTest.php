<?php

namespace Rasta\UserFetcher\Tests\Unit;

class ActivatorTest extends \Codeception\Test\Unit
{

    protected static $DefaultConfig = [
        'endpoint' => 'ras-user-fetcher',
        'handler' => 'Rasta\UserFetcher\Handler',
        'page_title' => 'Users Table',
        'snippet' => '<div id="ras-user-fetcher-details" /><div id="ras-user-fetcher" />'
    ];

    /**
     * @var \Rasta\UserFetcher\Activator
     */
    protected $instance;

    /**
    * Reference to the mocked dependency handler
    **/
    public $mockedHandler = 'Rasta\UserFetcher\Tests\Unit\MockHandler';



    public function __construct()
    {
        parent::__construct();
        $this->instance = new \Rasta\UserFetcher\Activator();
    }

    protected function _before()
    {
        // set fresh instance before each test
        $this->instance = new \Rasta\UserFetcher\Activator();
    }

    protected function activateMock()
    {
        $this->instance->setHandler($this->mockedHandler);
    }

    public function testDefaultConstruction()
    {
        // retrieve the DefaultConfig
        $DefaultConfig = self::$DefaultConfig;
        // if we construct new Instance without params, the default config should be used
        $this->assertEquals($DefaultConfig['endpoint'], $this->instance->getEndpoint());
        $this->assertEquals($DefaultConfig['handler'], $this->instance->getHandler());
        $this->assertEquals($DefaultConfig['page_title'], $this->instance->getPageTitle());
        $this->assertEquals($DefaultConfig['snippet'], $this->instance->getSnippet());
    }

    public function testCustomConstruction()
    {
        // define a custom config for Activator
        $config = [
            'endpoint'   => 'custom-endpoint',
            'handler'    => $this->mockedHandler,
            'page_title' => 'Custom Page Title',
            'snippet'    => 'Random snippet'
        ];
        // new instance with
        $this->instance = new \Rasta\UserFetcher\Activator(
            $config['endpoint'],
            $config['page_title'],
            $config['snippet'],
            $config['handler']
        );
        // now we check if our custom parameters are all set
        $this->assertEquals($config['endpoint'], $this->instance->getEndpoint());
        $this->assertEquals($config['handler'], $this->instance->getHandler());
        $this->assertEquals($config['page_title'], $this->instance->getPageTitle());
        $this->assertEquals($config['snippet'], $this->instance->getSnippet());
    }

    public function testGetterAndSetterForEndpoint()
    {
        // lets set and get a custom endpoint
        $custom_endpoint = 'get-set-endpoint';
        $this->assertEquals($custom_endpoint, $this->instance->setEndpoint($custom_endpoint)->getEndpoint());
        // endpoint should be nullable, we fall back to default via getter
        $default = self::$DefaultConfig['endpoint'];
        $this->assertEquals($default, $this->instance->setEndpoint(null)->getEndpoint());
    }

    public function testGetterAndSetterForHandler()
    {
        // lets set and get a custom endpoint
        $handler = $this->mockedHandler;
        $this->assertEquals($handler, $this->instance->setHandler($handler)->getHandler());
        // handler should be nullable, we fall back to default via getter
        $default = self::$DefaultConfig['handler'];
        $this->assertEquals($default, $this->instance->setHandler(null)->getHandler());
    }

    public function testGetterAndSetterForPageTitle()
    {
        // lets set and get a custom endpoint
        $test_title = 'Test Page Title';
        $this->assertEquals($test_title, $this->instance->setPageTitle($test_title)->getPageTitle());
        // handler should be nullable, we fall back to default via getter
        $default = self::$DefaultConfig['page_title'];
        $this->assertEquals($default, $this->instance->setPageTitle(null)->getPageTitle());
    }

    public function testGetterAndSetterForSnippet()
    {
        // lets set and get a custom endpoint
        $test_snippet = '<b>SnipSnap</b>';
        $this->assertEquals($test_snippet, $this->instance->setSnippet($test_snippet)->getSnippet());
        // handler should be nullable, we fall back to default via getter
        $default = self::$DefaultConfig['snippet'];
        $this->assertEquals($default, $this->instance->setSnippet(null)->getSnippet());
    }

    public function testGetterAndSetterForPage()
    {
        $this->activateMock();
        // lets set a custom page array
        $custom_page = ['id' => 666, 'title' => 'my title', 'body' => 'my body text'];
        //lets try the normal getter/setter functionality
        $this->assertEquals($custom_page, $this->instance->setPage($custom_page)->getPage());
        
        $mocked_response = [
                'id' => 1,
                'title' => 'Valid page title',
                'body' => 'some body text'
            ];
        // we need to bust cached page first, we set a new endpoint for doing that
        $this->assertEquals($mocked_response, $this->instance->setEndpoint('valid-path')->getPage());
        // getPage should return false if the endpoint path is invalid
        $this->assertFalse($this->instance->setEndpoint('invalid-path')->getPage());
    }

    public function testDoubleActivation(){
        $this->activateMock();
        // we activate it for the first time, we expect TRUE as success
        $this->assertTrue($this->instance->activate());
        // if we try that again a couple of times, since the page already exists, we'll expect always FALSE
        for ($x = 0; $x <= 100; $x++) {
            $this->assertFalse($this->instance->activate());
        }
        
    }

    public function testInvalidEndpointActivation(){
        // test when wp refuse to create new page, 
        $this->activateMock();
        // here: invalid-post-path is refused by the handler
        $this->assertFalse($this->instance->setEndpoint('invalid-post-path')->activate());
    }
}
