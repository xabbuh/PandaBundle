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
class EncodingTransformer extends BaseTransformer
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
     * @return \Xabbuh\PandaBundle\Model\Encoding[] The transformed Encodings
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
        $this->setModelProperties($encoding, $object);
        return $encoding;
    }
}
