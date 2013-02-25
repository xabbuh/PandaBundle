<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Xabbuh\PandaBundle\Event\EncodingCompleteEvent;
use Xabbuh\PandaBundle\Event\EncodingProgressEvent;
use Xabbuh\PandaBundle\Event\VideoCreatedEvent;
use Xabbuh\PandaBundle\Event\VideoEncodedEvent;

/**
 * XabbuhPandaBundle controllers.
 * 
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Controller extends ContainerAware
{
    /**
     * Sign a set of given url parameters and return them as a JSON object.
     * 
     * Specials keys are <em>method</em> and <em>path</em>. <em>method</em> is
     * the http method and <em>path</em> the url part of the api request for
     * which the signature is generated. The default <em>method</em> if not
     * given is <em>GET</em>, the default <em>path</em> is <em>/videos.json</em>.
     * All remaining url parameters are treated as parameters for the api
     * request.
     *
     * @param string $cloud Cloud to use for performing API requests
     * @return \Symfony\Component\HttpFoundation\JsonResponse The response
     */
    public function signAction($cloud)
    {
        $request = $this->getRequest();
        $params = $request->query->all();
        if(isset($params["method"])) {
            $method = $params["method"];
            unset($params["method"]);
        } else {
            $method = "GET";
        }
        if(isset($params["path"])) {
            $path = $params["path"];
            unset($params["path"]);
        } else {
            $path = "/videos.json";
        }
        $cloudManager = $this->getCloudManager();
        $restClient = $cloudManager->getCloud($cloud)->getPandaApi()->getRestClient();
        $content = $restClient->signParams($method, $path, $params);
        return new JsonResponse($content);
    }

    /**
     * Authorize a file upload.
     *
     * The name of the file to upload and its size are passed as POST parameters.
     * The response contains a JSON encoded object. It includes a property
     * upload_url to which the caller should send its video file.
     *
     * @param $cloud Cloud to use for performing API requests
     * @return \Symfony\Component\HttpFoundation\JsonResponse The response
     */
    public function authoriseUploadAction($cloud)
    {
        $request = $this->getRequest();
        $payload = json_decode($request->request->get("payload"));
        $cloudManager = $this->getCloudManager();
        $restClient = $cloudManager->getCloud($cloud)->getPandaApi()->getRestClient();
        $upload = json_decode($restClient->post(
            "/videos/upload.json",
            array(
                "file_name" => $payload->filename,
                "file_size" => $payload->filesize,
                "use_all_profiles" => true
            )
        ));
        return new JsonResponse(array("upload_url" => $upload->location));
    }
    
    /**
     * Endpoint for notification requests.
     * 
     * @return \Symfony\Component\HttpFoundation\Response An empty response with status code 200
     */
    public function notifyAction()
    {
        $request = $this->getRequest();
        $params = $request->request;
        
        // dispatch notification events depending on the given event
        $eventDispatcher = $this->container->get("event_dispatcher");
        $videoId = $params->get("video_id");
        $eventName = $params->get("event");
        switch($eventName) {
            case "video-created":
                $encodingIds = $params->get("encoding_ids");
                $event = new VideoCreatedEvent($videoId, $encodingIds);
                $eventDispatcher->dispatch("xabbuh_panda.video_created", $event);
                break;
            case "video-encoded":
                $encodingIds = $params->get("encoding_ids");
                $event = new VideoEncodedEvent($videoId, $encodingIds);
                $eventDispatcher->dispatch("xabbuh_panda.video_encoded", $event);
                break;
            case "encoding-progress":
                $encodingId = $params->get("encoding_id");
                $progress = $params->get("progress");
                $event = new EncodingProgressEvent($videoId, $encodingId, $progress);
                $eventDispatcher->dispatch("xabbuh_panda.encoding_progress", $event);
                break;
            case "encoding-complete":
                $encodingId = $params->get("encoding_id");
                $event = new EncodingCompleteEvent($videoId, $encodingId);
                $eventDispatcher->dispatch("xabbuh_panda.encoding_complete", $event);
                break;
            default:
                return new Response("Invalid or missing event name.", 400);
                break;
        }
        
        // return an empty 200 OK response
        return new Response("");
    }

    /**
     * Get the current request
     *
     * @return \Symfony\Component\HttpFoundation\Request The request
     */
    private function getRequest()
    {
        return $this->container->get("request");
    }

    /**
     * Get the configured cloud manager.
     *
     * @return \Xabbuh\PandaBundle\Cloud\CloudManagerInterface The cloud manager
     */
    private function getCloudManager()
    {
        return $this->container->get("xabbuh_panda.cloud_manager");
    }
}
