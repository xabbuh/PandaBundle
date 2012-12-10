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
use Xabbuh\PandaBundle\Model\Notifications;
use Xabbuh\PandaClient\PandaApi;

/**
 * Intuitive PHP interface for the Panda video encoding service API.
 * 
 * This client wraps an Panda API implementation. Every response of an API call
 * is passed to the data transformation layer which transforms the JSON response
 * of the webservice into a matching model object.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Client
{
    /**
     * The wrapped Panda API implementation
     * 
     * @var \Xabbuh\PandaClient\PandaApi
     */
    private $pandaApi;
    
    /**
     * The factory that creates and manages transformer instances
     * 
     * @var \Xabbuh\PandaBundle\Services\TransformerFactory
     */
    private $transformerFactory;
    
    
    /**
     * Constructs the Panda client service.
     * 
     * @param \Xabbuh\PandaClient\PandaApi $pandaApi The Panda API implementation being wrapped
     * @param \Xabbuh\PandaBundle\Services\TransformerFactory $transformerFactory The transformer factory
     */
    public function __construct(PandaApi $pandaApi, TransformerFactory $transformerFactory)
    {
        $this->pandaApi = $pandaApi;
        $this->transformerFactory = $transformerFactory;
    }
    
    /**
     * Returns the Panda API implementation.
     * 
     * @return \Xabbuh\PandaClient\PandaApi The Panda API implementation
     */
    public function getPandaApi()
    {
        return $this->pandaApi;
    }
    
    /**
     * Send a request to the Panda encoding service to encode a video file that
     * can be found under a particular url.
     * 
     * @param string $url The url where the encoding service can fetch the video
     * @return \Xabbuh\PandaBundle\Model\Video The created video
     */
    public function encodeVideoByUrl($url)
    {
        $response = $this->pandaApi->encodeVideoByUrl($url);
        $transformer = $this->transformerFactory->get("Video");
        return $transformer->fromJSON($response);
    }
    
    /**
     * Upload a video file to the Panda encoding service.
     * 
     * @param string $localPath The path to the local video file
     * @return \Xabbuh\PandaBundle\Model\Video The created video
     */
    public function encodeVideoFile($localPath)
    {
        $response = $this->pandaApi->encodeVideoFile($localPath);
        $transformer = $this->transformerFactory->get("Video");
        return $transformer->fromJSON($response);
    }
    
    /**
     * Retrieve the cloud's notifications configuration.
     * 
     * @return \Xabbuh\PandaBundle\Model\Notifications The notifications
     */
    public function getNotifications()
    {
        $transformer = $this->transformerFactory->get("Notifications");
        return $transformer->fromJSON($this->pandaApi->getNotifications());
    }
    
    /**
     * Change the notifications configuration.
     * 
     * @param \Xabbuh\PandaBundle\Model\Notifications $notifications The new configuration
     * @return \Xabbuh\PandaBundle\Model\Notifications The new configuration
     */
    public function setNotifications(Notifications $notifications)
    {
        $transformer = $this->transformerFactory->get("Notifications");
        $params = $transformer->toRequestParams($notifications);
        $response = $this->pandaApi->setNotifications($params->all());
        return $transformer->fromJSON($response);
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
        return $this->pandaApi->getRestClient()->signature($method, $path, $params);
    }
}
