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
class ProfileTransformer extends BaseTransformer
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
        $profiles = array();
        foreach ($json as $object) {
            $profiles[] = $this->fromObject($object);
        }
        return $profiles;
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
        $this->setModelProperties($profile, $object);
        return $profile;
    }
}
