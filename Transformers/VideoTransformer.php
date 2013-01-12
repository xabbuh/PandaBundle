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
        return $this->fromObject(json_decode($jsonString));
    }

    /**
     * Transform a JSON representation of a collection of videos into an array
     * of Video objects.
     *
     * @param string $jsonString The JSON string being transformed
     * @return array The transformed Videos
     */
    public function fromJSONCollection($jsonString)
    {
        $json = json_decode($jsonString);
        $videos = array();
        foreach ($json as $object) {
            $videos[] = $this->fromObject($object);
        }
        return $videos;
    }

    /**
     * Transform a standard PHP object into a Video instance.
     *
     * @param \stdClass $object The object being transformed
     * @return \Xabbuh\PandaBundle\Model\Video The transformed Video
     */
    private function fromObject(\stdClass $object)
    {
        $video = new Video();
        $video->setId($object->id);
        $video->setCreatedAt($object->created_at);
        $video->setUpdatedAt($object->updated_at);
        $video->setOriginalFilename($object->original_filename);
        $video->setExtname($object->extname);
        $video->setSourceUrl($object->source_url);
        $video->setDuration($object->duration);
        $video->setWidth($object->width);
        $video->setHeight($object->height);
        $video->setFileSize($object->file_size);
        $video->setVideoBitrate($object->video_bitrate);
        $video->setAudioBitrate($object->audio_bitrate);
        $video->setVideoCodec($object->video_codec);
        $video->setAudioCodec($object->audio_codec);
        $video->setFps($object->fps);
        $video->setAudioChannels($object->audio_channels);
        $video->setAudioSampleRate($object->audio_sample_rate);
        $video->setStatus($object->status);
        $video->setMimeType($object->mime_type);
        $video->setPath($object->path);
        return $video;
    }
}
