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
use Symfony\Component\HttpFoundation\Request;

/**
 * Create Panda events based on requests.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EventFactory
{
    /**
     * Creates a Panda event from the given request.
     *
     * @param Request $request The request to analyse
     *
     * @return Event The created event
     *
     * @throws \InvalidArgumentException if the request could not be converted into a valid event
     */
    public static function createEventFromRequest(Request $request)
    {
        switch($request->request->get('event')) {
            case 'video-created':
                return self::createVideoCreatedEventFromRequest($request);
                break;
            case 'video-encoded':
                return self::createVideoEncodedEventFromRequest($request);
                break;
            case 'encoding-progress':
                return self::createEncodingProgressEventFromRequest($request);
                break;
            case 'encoding-complete':
                return self::createEncodingCompleteEventFromRequest($request);
                break;
            default:
                throw new \InvalidArgumentException(
                    'The request does not contain a valid event name'
                );
                break;
        }
    }

    /**
     * Creates an EncodingCompleteEvent from the given request.
     *
     * @param Request $request The request to analyse
     *
     * @return EncodingCompleteEvent The created event
     *
     * @throws \InvalidArgumentException if some necessary data is not provided
     */
    public static function createEncodingCompleteEventFromRequest(Request $request)
    {
        $videoId = $request->request->get('video_id');
        $encodingId = $request->request->get('encoding_id');

        if (null === $videoId || !is_string($videoId)) {
            throw new \InvalidArgumentException('no valid video id given');
        }

        if (null === $encodingId || !is_string($encodingId)) {
            throw new \InvalidArgumentException('no valid encoding id given');
        }

        return new EncodingCompleteEvent($videoId, $encodingId);
    }

    /**
     * Creates an EncodingProgressEvent from the given request.
     *
     * @param Request $request The request to analyze
     *
     * @return EncodingProgressEvent The created event
     *
     * @throws \InvalidArgumentException if some necessary data is not provided
     */
    public static function createEncodingProgressEventFromRequest(Request $request)
    {
        $videoId = $request->request->get('video_id');
        $encodingId = $request->request->get('encoding_id');
        $progress = $request->request->get('progress');

        if (null === $videoId || !is_string($videoId)) {
            throw new \InvalidArgumentException('no valid video id given');
        }

        if (null === $encodingId || !is_string($encodingId)) {
            throw new \InvalidArgumentException('no valid encoding id given');
        }

        if (null === $progress || !is_int($progress)) {
            throw new \InvalidArgumentException('no valid progress given');
        }

        return new EncodingProgressEvent($videoId, $encodingId, $progress);
    }

    /**
     * Creates a VideoCreatedEvent from the given request.
     *
     * @param Request $request The request to analyze
     *
     * @return VideoCreatedEvent The created event
     *
     * @throws \InvalidArgumentException if some necessary data is not provided
     */
    public static function createVideoCreatedEventFromRequest(Request $request)
    {
        $videoId = $request->request->get('video_id');
        $encodingIds = $request->request->get('encoding_ids');

        if (null === $videoId || !is_string($videoId)) {
            throw new \InvalidArgumentException('no valid video id given');
        }

        if (null === $encodingIds || !is_array($encodingIds) || 0 == count($encodingIds)) {
            throw new \InvalidArgumentException('no valid encoding ids given');
        }

        return new VideoCreatedEvent($videoId, $encodingIds);
    }

    /**
     * Creates a VideoEncodedEvent from the given request.
     *
     * @param Request $request The request to analyze
     *
     * @return VideoEncodedEvent The created event
     *
     * @throws \InvalidArgumentException if some necessary data is not provided
     */
    public static function createVideoEncodedEventFromRequest(Request $request)
    {
        $videoId = $request->request->get('video_id');
        $encodingIds = $request->request->get('encoding_ids');

        if (null === $videoId || !is_string($videoId)) {
            throw new \InvalidArgumentException('no valid video id given');
        }

        if (null === $encodingIds || !is_array($encodingIds) || 0 == count($encodingIds)) {
            throw new \InvalidArgumentException('no valid encoding ids given');
        }

        return new VideoEncodedEvent($videoId, $encodingIds);
    }
}
 