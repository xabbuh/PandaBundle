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

use Xabbuh\PandaBundle\Model\Profile;

/**
 * Transform various data representation formats into profiles and vice versa.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ProfileTransformer
{
    /**
     * Transform a JSON representation of a profile into a Profile object.
     *
     * @param string $jsonString The JSON string being transformed
     * @return \Xabbuh\PandaBundle\Model\Profile The transformed Profile
     */
    public function fromJSON($jsonString)
    {
        return $this->fromObject(json_decode($jsonString));
    }

    /**
     * Transform a JSON representation of a collection of profiles into an
     * array of Profile objects.
     *
     * @param string $jsonString The JSON string being transformed
     * @return array The transformed Profiles
     */
    public function fromJSONCollection($jsonString)
    {
        $json = json_decode($jsonString);
        if (is_array($json)) {
            $profiles = array();
            foreach ($json as $object) {
                $profiles[] = $this->fromObject($object);
            }
            return $profiles;
        } else {
            return $this->fromObject($json);
        }
    }

    /**
     * Transform a standard php object into a Profile instance
     *
     * @param \stdClass $object The object being transformed
     * @return \Xabbuh\PandaBundle\Model\Profile The transformed profile
     */
    private function fromObject(\stdClass $object)
    {
        $profile = new Profile();
        $profile->setId($object->id);
        $profile->setTitle($object->title);
        $profile->setName($object->name);
        $profile->setExtname($object->extname);
        $profile->setWidth($object->width);
        $profile->setHeight($object->height);
        $profile->setAudioBitrate($object->audio_bitrate);
        $profile->setVideoBitrate($object->video_bitrate);
        $profile->setAspectMode($object->aspect_mode);
        $profile->setCreatedAt($object->created_at);
        $profile->setUpdatedAt($object->updated_at);
        return $profile;
    }
}
