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

/**
 * Event that is triggered when a video was successfully encoded.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final since 1.5
 */
class VideoEncodedEvent extends Event
{
    /**
     * The VIDEO_ENCODED event occurs when a video was successfully encoded.
     *
     * The event listener method receives a Xabbuh\PandaBundle\Event\VideoCreatedEvent instance.
     */
    const NAME = 'xabbuh_panda.video_encoded';

    /**
     * The id of the encoded video
     * @var string
     */
    private $videoId;

    /**
     * List of encoding ids of this video
     * @var string[]
     */
    private $encodingIds = array();

    /**
     * Constructs a new VideoEncodedEvent.
     *
     * @param string   $videoId     Video id
     * @param string[] $encodingIds Ids of the video's encodings
     */
    public function __construct($videoId, array $encodingIds)
    {
        $this->videoId = $videoId;
        $this->encodingIds = $encodingIds;
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
     * @return string[] The encoding ids
     */
    public function getEncodingIds()
    {
        return $this->encodingIds;
    }
}
