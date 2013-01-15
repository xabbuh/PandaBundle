<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Cloud;

use Symfony\Component\DependencyInjection\Container;
use Xabbuh\PandaBundle\Model\Notifications;
use Xabbuh\PandaBundle\Services\TransformerFactory;
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
class Cloud
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
     * Retrieve a collection of videos from the server.
     *
     * @return array Array of Videos
     */
    public function getVideos()
    {
        $response = $this->pandaApi->getVideos();
        $transformer = $this->transformerFactory->get("Video");
        return $transformer->fromJSONCollection($response);
    }

    /**
     * Delete a video.
     *
     * @param $videoId The video being deleted
     * @return string The server response
     */
    public function deleteVideo($videoId)
    {
        return $this->pandaApi->deleteVideo($videoId);
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
     * Retrieve all profiles.
     *
     * @return array The list of profiles
     */
    public function getProfiles()
    {
        $transformer = $this->transformerFactory->get("Profile");
        return $transformer->fromJSONCollection($this->pandaApi->getProfiles());
    }

    /**
     * Retrieve informations for a profile.
     *
     * @param $profileId The id of the profile being fetched
     * @return \Xabbuh\PandaBundle\Model\Profile The profile
     */
    public function getProfile($profileId)
    {
        $transformer = $this->transformerFactory->get("Profile");
        return $transformer->fromJSON($this->pandaApi->getProfile($profileId));
    }
    
    /**
     * Receive encodings filtered by video from the server.
     *
     * @param $videoId Id of the video to filter by
     * @param array $filter Optional additional filters
     * @return array A collection of Encoding objects
     */
    public function getEncodingsForVideo($videoId, array $filter = null)
    {
        $response = $this->pandaApi->getEncodingsForVideo($videoId);
        $transformer = $this->transformerFactory->get("Encoding");
        return $transformer->fromJSONCollection($response);
    }

    /**
     * Fetch the cloud's data from the panda server.
     * 
     * @return \Xabbuh\PandaBundle\Model\Cloud The cloud's model data
     */
    public function getCloudData()
    {
        $response = $this->pandaApi->getCloud($this->pandaApi->getRestClient()->getCloudId());
        $transformer = $this->transformerFactory->get("Cloud");
        return $transformer->fromJSON($response);
    }

    /**
     * Change cloud data.
     *
     * @param $cloudId
     * @param array $data
     * @return string The server response
     */
    public function setCloud($cloudId, array $data)
    {
        return $this->pandaApi->setCloud($cloudId, $data);
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
