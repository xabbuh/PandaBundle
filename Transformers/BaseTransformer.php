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

/**
 * Common functions for all transformer classes.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class BaseTransformer
{
    const METHOD_TYPE_GET = 1;

    const METHOD_TYPE_SET = 2;


    /**
     * Get the getter or setter method name for a particular property.
     *
     * @param string $propertyName The property name in underscore style
     * @param int $methodType Method type (BaseTransformer::METHOD_TYPE_GET or BaseTransformer::METHOD_TYPE_SET)
     * @return string The generated method name in camel case style
     */
    protected function getMethodName($propertyName, $methodType)
    {
        if ($methodType == self::METHOD_TYPE_GET) {
            $methodName = "get";
        } else if ($methodType == self::METHOD_TYPE_SET) {
            $methodName = "set";
        }

        while (($pos = strpos($propertyName, "_")) !== false) {
            $tmp = substr($propertyName, 0, $pos);
            $tmp .= ucfirst(substr($propertyName, $pos + 1));
            $propertyName = $tmp;
        }
        return $methodName . ucfirst($propertyName);
    }

    /**
     * Read the properties from one object and set the properties of the
     * model respectively.
     *
     * @param $model The model which properties are being set
     * @param \stdClass $object The object from where the property values are read
     * @throws \InvalidArgumentException if a property of $object doesn't exist in $model
     */
    protected function setModelProperties($model, \stdClass $object)
    {
        foreach ($object as $name => $value) {
            $methodName = $this->getMethodName($name, self::METHOD_TYPE_SET);
            if (method_exists($model, $methodName)) {
                $model->$methodName($value);
            } else {
                throw new \InvalidArgumentException(
                    "Transformation error: Unknown property $name in model"
                );
            }
        }
    }
}
