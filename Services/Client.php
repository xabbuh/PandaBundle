<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Services;

use Symfony\Component\DependencyInjection\Container;

/**
 * Intuitive PHP interface for the Panda video encoding service API.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Client
{
    /**
     * Panda cloud id
     * 
     * @var string
     */
    private $cloudId;
    
    /**
     * API access key
     * 
     * @var string
     */
    private $accessKey;
    
    /**
     * API secret key
     * 
     * @var string
     */
    private $secretKey;
    
    /**
     * API host
     * 
     * @var string
     */
    private $apiHost;
    
    /**
     * The service container context
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    
    /**
     * Constructs the Panda client service.
     * 
     * @param string $cloudId Panda cloud id
     * @param string $accessKey API access key
     * @param string $secretKey API secret key
     * @param string $apiHost  API host
     * @param Symfony\Component\DependencyInjection\Container $container The service container
     */
    public function __construct($cloudId, $accessKey, $secretKey, $apiHost, Container $container)
    {
        $this->cloudId = $cloudId;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->apiHost = $apiHost;
        $this->container = $container;
    }
    
    /**
     * Send a request to the Panda encoding service to encode a video file that
     * can be found under a particular url.
     * 
     * @param string $url The url where the encoding service can fetch the video
     * @return Xabbuh\PandaBundle\Model\Video The created video
     */
    public function encodeVideoByUrl($url)
    {
        $response = $this->post("/videos.json", array("source_url" => $url));
        $transformer = $this->container->get("xabbuh_panda.transformers.video");
        return $transformer->transform($response);
    }
    
    /**
     * Upload a video file to the Panda encoding service.
     * 
     * @param string $localPath The path to the local video file
     * @return Xabbuh\PandaBundle\Model\Video The created video
     */
    public function encodeVideoFile($localPath)
    {
        $response = $this->post("/videos.json", array("file" => "@$localPath"));
        $transformer = $this->container->get("xabbuh_panda.transformers.video");
        return $transformer->transform($response);
    }
    
    /**
     * Helper method to send GET requests to the Panda API.
     * 
     * @param string $path The path to send requests to
     * @param array $params Url parameters
     * @return string The server response
     */
    private function get($path, array $params = array())
    {
        return $this->request("GET", $path, $params);
    }
    
    /**
     * Helper method to send POST requests to the Panda API.
     * 
     * @param string $path The path to send requests to
     * @param array $params Url parameters
     * @return string The server response
     */
    private function post($path, array $params = array())
    {
        return $this->request("POST", $path, $params);
    }
    
    /**
     * Helper method to send PUT requests to the Panda API.
     * 
     * @param string $path The path to send requests to
     * @param array $params Parameters
     * @return string The server response
     */
    private function put($path, array $params = array())
    {
        return $this->request("PUT", $path, $params);
    }
    
    /**
     * Helper method to send DELETE requests to the Panda API.
     * 
     * @param string $path The path to send requests to
     * @param array $params Parameters
     * @return string The server response
     */
    private function delete($path, array $params = array())
    {
        return $this->request("DELETE", $path, $params);
    }
    
    /**
     * Send HTTP requests to the API server.
     * 
     * @param string $method HTTP method (GET, POST, PUT or DELETE)
     * @param string $path Request path
     * @param array $params Additional request parameters
     * @return string The API server's response
     */
    private function request($method, $path, array $params)
    {
        // sign the request parameters
        $params = $this->signParams($method, $path, $params);
        
        // build url, append url parameters if the request method is GET or DELETE
        $url = "https://{$this->apiHost}/v2{$path}";
        if($method == "GET" || $method == "DELETE") {
            $url .= "?" . http_build_query($params);
        }
            
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // include parameters in the request body for POST and PUT requests
        if($method == "POST" || $method == "PUT") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    /**
     * Generate signature for a given set of request parameters and add the
     * signature to the parameters.
     * 
     * @param string $method The HTTP method
     * @param string $path The request path
     * @param array $params Request parameters
     * @return array The signed parameters 
     */
    public function signParams($method, $path, array $params)
    {
        // add authentication data to the request parameters if not set
        if(!isset($params["cloud_id"])) {
            $params["cloud_id"] = $this->cloudId;
        }
        if(!isset($params["access_key"])) {
            $params["access_key"] = $this->accessKey;
        }
        if(!isset($params["timestamp"])) {
            $oldTz = date_default_timezone_get();
            date_default_timezone_set("UTC");
            $params["timestamp"] = date("c");
            date_default_timezone_set($oldTz);
        }
        
        // generate the signature
        $params["signature"] = $this->signature($method, $path, $params);
        
        return $params;
    }
    
    /**
     * Generates the signature for an API requests based on its parameters.
     * 
     * @param string $method The HTTP method
     * @param string $path The request path
     * @param array $params Request parameters
     * @return string The generated signature
     */
    public function signature($method, $path, array $params)
    {
        ksort($params);
        if(isset($params["file"])) {
            unset($params["file"]);
        }
        $canonicalQueryString = strtr(http_build_query($params), "+", "%20");
        $stringToSign = sprintf("%s\n%s\n%s\n%s", strtoupper($method), $this->apiHost, $path, $canonicalQueryString);
        $hmac = hash_hmac("sha256", $stringToSign, $this->secretKey, true);
        return base64_encode($hmac);
    }
}
