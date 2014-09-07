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
 * Event that is triggered repeatedly while an encoding process is running.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EncodingProgressEvent extends Event
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
     * The encoding progress
     * @var integer
     */
    private $progress;

    /**
     * Constructs a new EncodingProgressEvent.
     *
     * @param string $videoId    Video id
     * @param string $encodingId Encoding id
     * @param integer $progress  Progress of the encoding
     */
    public function __construct($videoId, $encodingId, $progress)
    {
        $this->videoId = $videoId;
        $this->encodingId = $encodingId;
        $this->progress = $progress;
        $this->setName("xabbuh_panda.encoding_progress");
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

    /**
     * Returns the encoding progress.
     *
     * @return integer The progress
     */
    public function getProgress()
    {
        return $this->progress;
    }
}
