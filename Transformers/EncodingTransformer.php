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

use Xabbuh\PandaBundle\Model\Encoding;

/**
 * Transform Encodings from and to various data representation formats.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EncodingTransformer
{
    /**
     * Convert a JSON encoded string into an Encoding object.
     *
     * @param string $jsonString The JSON string being transformed
     * @return \Xabbuh\PandaBundle\Model\Encoding The transformed Encoding
     */
    public function fromJSON($jsonString)
    {
        return $this->fromObject(json_decode($jsonString));
    }
    
    /**
     * Convert a JSON encoded collection of encodings into an array of Encoding
     * objects.
     *
     * @param string $jsonString The JSON string being transformed
     * @return array The transformed Encodings
     */
    public function fromJSONCollection($jsonString)
    {
        $json = json_decode($jsonString);
        $encodings = array();
        foreach ($json as $object) {
            $encodings[] = $this->fromObject($object);
        }
        return $encodings;
    }
    
    /**
     * Transform a standard php object into an Encoding instance
     *
     * @param \stdClass $object The object being transformed
     * @return \Xabbuh\PandaBundle\Model\Encoding The transformed encoding
     */
    private function fromObject(\stdClass $object)
    {
        $encoding = new Encoding();
        $encoding->setId($object->id);
        $encoding->setVideoId($object->video_id);
        $encoding->setExtname($object->extname);
        $encoding->setPath($object->path);
        $encoding->setProfileId($object->profile_id);
        $encoding->setProfileName($object->profile_name);
        $encoding->setStatus($object->status);
        $encoding->setEncodingProgress($object->encoding_progress);
        $encoding->setWidth($object->width);
        $encoding->setHeight($object->height);
        $encoding->setStartedEncodingAt($object->started_encoding_at);
        $encoding->setEncodingTime($object->encoding_time);
        foreach ($object->files as $file) {
            $encoding->addFile($file);
        }
        $encoding->setCreatedAt($object->created_at);
        $encoding->setUpdatedAt($object->updated_at);
        return $encoding;
    }
}
