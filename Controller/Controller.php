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
     * @return \Symfony\Component\HttpFoundation\JsonResponse The response
     */
    public function signAction()
    {
        $request = $this->container->get("request");
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
        $client = $this->container->get("xabbuh_panda.client");
        $content = $client->signParams($method, $path, $params);
        return new JsonResponse($content);
    }
    
    /**
     * Endpoint for notification requests.
     * 
     * @return \Symfony\Component\HttpFoundation\Response An empty response with status code 200
     */
    public function notifyAction()
    {
        $request = $this->container->get("request");
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
        }
        
        // return an empty 200 OK response
        return new Response("");
    }
}
