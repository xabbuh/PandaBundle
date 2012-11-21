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
}
