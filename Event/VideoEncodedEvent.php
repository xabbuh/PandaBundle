<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Xabbuh\PandaBundle\XabbuhPandaEvents;

/**
 * Event that is triggered when a video was successfully encoded.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoEncodedEvent extends Event
{
    /**
     * The id of the encoded video
     * @var string
     */
    private $videoId;
    
    /**
     * List of encoding ids of this video
     * @var array
     */
    private $encodingIds = array();
    
    /**
     * Constructs a new VideoEncodedEvent.
     * 
     * @param string $videoId Video id
     * @param array $encodingIds Ids of the video's encodings
     */
    public function __construct($videoId, array $encodingIds)
    {
        $this->videoId = $videoId;
        $this->encodingIds = $encodingIds;
        $this->setName(XabbuhPandaEvents::VIDEO_ENCODED);
    }
    
    /**
     * Returns the video id.
     * 
     * @return string The video id
     */
    public function getVideoId()
    {
        return $this->videoId;
    }
    
    /**
     * Returns the video's encoding ids.
     * 
     * @return array The encoding ids
     */
    public function getEncodingIds()
    {
        return $this->encodingIds;
    }
}
