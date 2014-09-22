<?php

namespace DruidDriver;
require_once __DIR__ . '/../../vendor/autoload.php';

use Guzzle\Http\Client as Client;
use Guzzle\Http\Message\Response as Response;

/**
 * Class DruidConnection
 * @package   DruidDriver\DruidConnection
 * @author    Ernesto Spiro Peimbert Andreakis
 * @version   1.0
 * @category  WebPT
 * @copyright Copyright (c) 2014 WebPT, Inc.
 */
class DruidConnection {
    /**
     * http protocol
     */
    const HTTP = 'http';
    /**
     * https protocol
     */
    const SECURE_HTTP = 'https';
    /**
     * Endpoint host
     * @var string
     * @access protected
     */
    protected $host;
    /**
     * Endpoint protocol
     * @var string
     * @access protected
     */
    protected $protocol;
    /**
     * Enpoint port
     * @var integer
     * @access protected
     */
    protected $port;
    /**
     * Endpoint path
     * @var string
     * @access protected
     */
    protected $path;
    /**
     * The query to be executed
     * @var DruidQueryInterface
     */
    protected $query;
    /**
     * The instance of the object (Singleton)
     * @var Connection
     * @access protected
     */
    protected static $instance;
    /**
     * The endpoint's URL built from protocol, host, port and path
     * @var string
     */
    protected $url;
    /**
     * @var Client
     */
    protected $guzzleObject;
    /**
     * @var Response
     */
    protected $response;

    /**
     * Class constructor
     * Enforce Singleton pattern
     * @access protected
     */
    protected function __construct(){}

    /**
     * Clone
     * Enforce Singleton pattern
     * @access protected
     */
    protected function __clone(){}

    /**
     * Get the instance
     * @return Connection
     */
    public function &getInstance(){
        if(!is_object(self::$instance) || !(self::$instance instanceof Connection)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Returns the host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the host
     *
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Returns the port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the port
     *
     * @param int $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Returns the protocol
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Sets the protocol
     *
     * @param string $protocol
     *
     * @return $this
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Returns the query
     *
     * @return DruidQueryInterface
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets the query
     *
     * @param DruidQueryInterface $query
     *
     * @return $this
     */
    public function setQuery(DruidQueryInterface $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Executes the query
     * @param $method
     */
    public function executeQuery($method){
        $this->buildURL();
        $request = $this->getGuzzleObject()->createRequest($method,$this->getUrl());
        $this->setResponse($this->getGuzzleObject()->send($request));
    }

    /**
     * Returns the url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Builds the endpoint's URL
     * @return string
     */
    public function buildUrl(){
        $port = (is_numeric($this->getPort()))?":{$this->getPort()}":'';
        $this->setUrl("{$this->getProtocol()}://{$this->getHost()}{$port}/{$this->getPath()}");
        return $this->getUrl();
    }

    /**
     * Check if the endpoint is available
     * It actually does a HEAD request and checks for a 200 response
     * @return bool
     */
    public function isConnectionAvailable(){
        $this->setResponse($this->guzzleObject->head($this->getUrl())->send());
        $response = $this->getResponse();
        $code = $response->getStatusCode();
        if(200 != $code) {
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * Returns the guzzleObject
     *
     * @return Client
     */
    public function getGuzzleObject()
    {
        if(!($this->guzzleObject instanceof Client)){
            $this->guzzleObject = new Client();
        }
        return $this->guzzleObject;
    }

    /**
     * Returns the response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response
     *
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Returns the current config values for the object
     * @param bool $asString
     *
     * @return array|bool
     */
    public function getConfig($asString=false){
        $config = array();
        $config['protocol'] = $this->getProtocol();
        $config['host'] = $this->getHost();
        $config['port'] = $this->getPort();
        $config['path'] = $this->getPath();
        $config['url'] = $this->getUrl();
        $config['instance'] = get_class(self::$instance);
        $config['guzzleObject'] = get_class($this->getGuzzleObject());
        if($asString) {
            $returnString = 'Object config:' . PHP_EOL;
            foreach($config as $key=>$value) {
                $returnString .= "\t{$key} = {$value}" . PHP_EOL;
            }
            return $returnString;
        }
        else{
            return $config;
        }
    }
}
$con = DruidConnection::getInstance();
$con->setProtocol(DruidConnection::HTTP);
$con->setHost('google.com');
$con->buildUrl();
echo $con->getConfig(true);
$con->isConnectionAvailable();