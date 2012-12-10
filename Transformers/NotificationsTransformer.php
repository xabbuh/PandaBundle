<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Transformers;

use Symfony\Component\HttpFoundation\ParameterBag;
use Xabbuh\PandaBundle\Model\NotificationEvent;
use Xabbuh\PandaBundle\Model\Notifications;

/**
 * Transformation from variuos data representation formats into Notifications
 * model objects and vice versa.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de> 
 */
class NotificationsTransformer
{
    /**
     * Transform the JSON representation of notifications into a Notifications
     * model object.
     * 
     * @param string $jsonString The string in json format being transformed
     * @return Xabbuh\PandaBundle\Model\Notifications The notifications
     */
    public function fromJSON($jsonString)
    {
        $json = json_decode($jsonString);
        $notifications = new Notifications();
        $notifications->setUrl($json->url);
        $notifications->addNotificationEvent(
            new NotificationEvent("video_created", $json->events->video_created));
        $notifications->addNotificationEvent(
            new NotificationEvent("video_encoded", $json->events->video_encoded));
        $notifications->addNotificationEvent(
            new NotificationEvent("encoding_progress", $json->events->encoding_progress));
        $notifications->addNotificationEvent(
            new NotificationEvent("encoding_completed", $json->events->encoding_completed));
        return $notifications;
    }
    
    /**
     * Transform a Notifications object into a ParameterBag of request parameters.
     * 
     * @param \Xabbuh\PandaBundle\Model\Notifications $notifications The notifications to transform
     * @return \Symfony\Component\HttpFoundation\ParameterBag The request parameters
     */
    public function toRequestParams(Notifications $notifications)
    {
        $params = new ParameterBag();
        if ($notifications->getUrl() != null) {
            $params->set("url", $notifications->getUrl());
        }
        $videoCreatedEvent = $notifications->getNotificationEvent("video_created");
        if ($videoCreatedEvent) {
            $params->set(
                "events[video_created]",
                $videoCreatedEvent->isActive() ? "true" : "false"
            );
        }
        $videoEncodedEvent = $notifications->getNotificationEvent("video_encoded");
        if ($videoEncodedEvent) {
            $params->set(
                "events[video_encoded]",
                $videoEncodedEvent->isActive() ? "true" : "false"
            );
        }
        $encodingProgressEvent = $notifications->getNotificationEvent("encoding_progress");
        if ($encodingProgressEvent) {
            $params->set(
                "events[encoding_progress]",
                $encodingProgressEvent->isActive() ? "true" : "false"
            );
        }
        $encodingCompletedEvent = $notifications->getNotificationEvent("encoding_completed");
        if ($encodingCompletedEvent) {
            $params->set(
                "events[encoding_completed]",
                $encodingCompletedEvent->isActive() ? "true" : "false"
            );
        }
        return $params;
    }
}
