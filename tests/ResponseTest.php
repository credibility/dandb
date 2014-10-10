<?php namespace Credibility\DandB;

use GuzzleHttp\Exception\ParseException;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class ResponseTest extends PHPUnit_Framework_TestCase {

    /** @var Response */
    protected $response;

    /** @var MockInterface */
    protected $responseInterface;

    public function setUp()
    {
        $this->responseInterface = m::mock('GuzzleHttp\Message\ResponseInterface');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testConstructionSuccess()
    {
        $this->setupResponseInterface(array(
            'test' => 'value',
            'meta' => array(
                'code' => 200
            )
        ));

        $this->assertArrayHasKey('test', $this->response->toArray());
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testConstructionNoMeta()
    {
        $this->setupResponseInterface(array(
            'test' => 'value',
        ));

        $this->assertArrayHasKey('test', $this->response->toArray());
        $this->assertEquals(500, $this->response->getStatusCode());
    }

    /**
     * @expectedException GuzzleHttp\Exception\TransferException
     */
    public function testThrowingParseException()
    {
        $this->responseInterface->shouldReceive('json')
            ->andThrow('GuzzleHttp\Exception\TransferException');

        $this->response = new Response($this->responseInterface);
    }

    public function testGetResponseDataSuccess()
    {
        $this->setupResponseInterface(array(
            'response' => array(
                'results' => array(
                    'test' => 'value',
                    'other_test' => 'other_value'
                )
            )
        ));

        $array = $this->response->getResponseData();
        $this->assertArrayHasKey('test', $array);
        $this->assertArrayHasKey('other_test', $array);
    }

    public function testGetResponseDataReturnsFalse()
    {
        $this->setupResponseInterface(array());

        $this->assertFalse($this->response->getResponseData());
    }

    public function testGetErrorsSuccess()
    {
        $this->setupResponseInterface(array(
            'error' => array(
                'error_message', 'another_error_message'
            )
        ));

        $this->assertCount(2, $this->response->getErrors());
    }

    public function testGetErrorsFails()
    {
        $this->setupResponseInterface(array());

        $this->assertFalse($this->response->getErrors());
    }

    public function testIsValidIsTrue()
    {
        $this->setupResponseInterface(array(
            'meta' => array(
                'code' => 200
            )
        ));

        $this->assertTrue($this->response->isValid());
    }

    public function testIsValidIsFalse()
    {
        $this->setupResponseInterface(array(
            'meta' => array(
                'code' => 400
            )
        ));

        $this->assertFalse($this->response->isValid());
    }

    private function setupResponseInterface($data)
    {
        $this->responseInterface->shouldReceive('json')
            ->andReturn($data);

        $this->response = new Response($this->responseInterface);
    }
}
 