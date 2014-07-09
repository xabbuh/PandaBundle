<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle;

/**
 * Contains all events thrown in the XabbuhPandaBundle
 *
 * @author Thomas Gallice <tmgallice@gmail.com>
 */
final class XabbuhPandaEvents
{
    /**
     * The ENCODING_COMPLETE event occurs when an encoding is done or failed.
     *
     * The event listener method receives a Xabbuh\PandaBundle\Event\EncodingCompleteEvent instance.
     */
    const ENCODING_COMPLETE = 'xabbuh_panda.encoding_complete';

    /**
     * The ENCODING_PROGRESS event occurs repeatedly while an encoding process is running.
     *
     * The event listener method receives a Xabbuh\PandaBundle\Event\EncodingProgressEvent instance.
     */
    const ENCODING_PROGRESS = 'xabbuh_panda.encoding_progress';

    /**
     * The VIDEO_CREATED event occurs when a video was successfully created.
     *
     * The event listener method receives a Xabbuh\PandaBundle\Event\VideoCreatedEvent instance.
     */
    const VIDEO_CREATED = 'xabbuh_panda.video_created';

    /**
     * The VIDEO_ENCODED event occurs when a video was successfully encoded.
     *
     * The event listener method receives a Xabbuh\PandaBundle\Event\VideoCreatedEvent instance.
     */
    const VIDEO_ENCODED = 'xabbuh_panda.video_encoded';
}
