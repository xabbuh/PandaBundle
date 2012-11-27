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

use Xabbuh\PandaBundle\Model\NotificationEvent;
use Xabbuh\PandaBundle\Model\Notifications;

/**
 * Transformation between a JSON string as returned by the Panda webservice into
 * a Notifications model object and vice versa.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de> 
 */
class JsonToNotificationsTransformer
{
    /**
     * Transform the JSON representation of notifications a Notifications model
     * object.
     * 
     * @param string $jsonString The string in json format being transformed
     * @return Xabbuh\PandaBundle\Model\Notifications The notifications
     */
    public function transform($jsonString)
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
     * Transform a Notifications object back into a JSON encoded string.
     * 
     * @param Xabbuh\PandaBundle\Model\Notifications $notifications The notifications being transformed
     * @return string The transformed JSON object
     */
    public function reverseTransform(Notifications $notifications)
    {
        $jsonObject = new \stdClass();
        $jsonObject->url = $notifications->getUrl();
        $jsonObject->events = new \stdClass();
        $videoCreatedEvent = $notifications->getNotificationEvent("video_created");
        if ($videoCreatedEvent) {
            $jsonObject->events->video_created = $videoCreatedEvent->isActive();
        }
        $videoEncodedEvent = $notifications->getNotificationEvent("video_encoded");
        if ($videoEncodedEvent) {
            $jsonObject->events->video_encoded = $videoEncodedEvent->isActive();
        }
        $encodingProgressEvent = $notifications->getNotificationEvent("encoding_progress");
        if ($encodingProgressEvent) {
            $jsonObject->events->encoding_progress = $encodingProgressEvent->isActive();
        }
        $encodingCompletedEvent = $notifications->getNotificationEvent("encoding_completed");
        if ($encodingCompletedEvent) {
            $jsonObject->events->encoding_completed = $encodingCompletedEvent->isActive();
        }
        return json_encode($jsonObject);
    }
    
    /**
     * Transform a Notifications object into an array of request parameters which
     * can be used in api requests.
     * 
     * @param \Xabbuh\PandaBundle\Model\Notifications $notifications The notifications to transform
     * @return array The request parameters
     */
    public function toRequestParams(Notifications $notifications)
    {
        $params = array();
        if ($notifications->getUrl() != null) {
            $params["url"] = $notifications->getUrl();
        }
        $videoCreatedEvent = $notifications->getNotificationEvent("video_created");
        if ($videoCreatedEvent) {
            $params["events[video_created]"] = $videoCreatedEvent->isActive() ? "true" : "false";
        }
        $videoEncodedEvent = $notifications->getNotificationEvent("video_encoded");
        if ($videoEncodedEvent) {
            $params["events[video_encoded]"] = $videoEncodedEvent->isActive() ? "true" : "false";
        }
        $encodingProgressEvent = $notifications->getNotificationEvent("encoding_progress");
        if ($encodingProgressEvent) {
            $params["events[encoding_progress]"] = $encodingProgressEvent->isActive() ? "true" : "false";
        }
        $encodingCompletedEvent = $notifications->getNotificationEvent("encoding_completed");
        if ($encodingCompletedEvent) {
            $params["events[encoding_completed]"] = $encodingCompletedEvent->isActive() ? "true" : "false";
        }
        return $params;
    }
}
