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
 * Event that is triggered when an encoding has successfully finished.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EncodingCompleteEvent extends Event
{
    /**
     * The id of the encoded video
     * @var string
     */
    private $videoId;

    /**
     * The id of the encoding
     * @var string
     */
    private $encodingId;

    /**
     * Constructs a new EncodingCompleteEvent.
     *
     * @param string $videoId    Video id
     * @param string $encodingId Encoding id
     */
    public function __construct($videoId, $encodingId)
    {
        $this->videoId = $videoId;
        $this->encodingId = $encodingId;
        $this->setName("xabbuh_panda.encoding_complete");
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
     * Returns the encoding id.
     *
     * @return string The encoding id
     */
    public function getEncodingId()
    {
        return $this->encodingId;
    }
}
