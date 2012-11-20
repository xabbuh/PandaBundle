<?php

namespace Xabbuh\PandaBundle\Services;

use Xabbuh\PandaBundle\Model\Video;

class JsonToVideoTransformer
{
    public function transform($jsonString)
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
    
    public function reverseTransform(Video $video)
    {
        
    }
}
