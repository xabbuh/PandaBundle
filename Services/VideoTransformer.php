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

use Xabbuh\PandaBundle\Model\Video;

/**
 * Transformation from various data representation formats into Video models and
 * vice versa.
 * 
 * A JSON object returned by the Panda api service may contain the following
 * properties:
 * <ul>
 *   <li>id</li>
 *   <li>created_at</li>
 *   <li>updated_at</li>
 *   <li>original_filename</li>
 *   <li>extname</li>
 *   <li>source_url</li>
 *   <li>duration</li>
 *   <li>width</li>
 *   <li>height</li>
 *   <li>file_size</li>
 *   <li>video_bitrate</li>
 *   <li>audio_bitrate</li>
 *   <li>video_codec</li>
 *   <li>audio_codec</li>
 *   <li>fps</li>
 *   <li>audio_channels</li>
 *   <li>audio_sample_rate</li>
 *   <li>status</li>
 *   <li>mime_type</li>
 *   <li>path</li>
 * </ul>
 * 
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoTransformer
{
    /**
     * Transform the JSON representation of a video into a Video model object.
     * 
     * @param string $jsonString JSON representation of a video
     * @return Xabbuh\PandaBundle\Model\Video The generated model object
     */
    public function fromJSON($jsonString)
    {
        $json = json_decode($jsonString);
        $video = new Video();
        $video->setId($json->id);
        $video->setCreatedAt($json->created_at);
        $video->setUpdatedAt($json->updated_at);
        $video->setOriginalFilename($json->original_filename);
        $video->setExtname($json->extname);
        $video->setSourceUrl($json->source_url);
        $video->setDuration($json->duration);
        $video->setWidth($json->width);
        $video->setHeight($json->height);
        $video->setFileSize($json->file_size);
        $video->setVideoBitrate($json->video_bitrate);
        $video->setAudioBitrate($json->audio_bitrate);
        $video->setVideoCodec($json->video_codec);
        $video->setAudioCodec($json->audio_codec);
        $video->setFps($json->fps);
        $video->setAudioChannels($json->audio_channels);
        $video->setAudioSampleRate($json->audio_sample_rate);
        $video->setStatus($json->status);
        $video->setMimeType($json->mime_type);
        $video->setPath($json->path);
        return $video;
    }
}
