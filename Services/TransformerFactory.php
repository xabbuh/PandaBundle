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

/**
 * Factory for retrieving model transformers.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class TransformerFactory
{
    /**
     * The generated transformers
     * 
     * @var array
     */
    private $transformers = array();
    
    
    /**
     * Returns the transformer for a model.
     *
     * @param string $model The model for which the transformer instance is returned
     * @return mixed The transformer instance
     */
    public function get($model)
    {
        if (!isset($this->transformers[$model])) {
            $class = "Xabbuh\\PandaBundle\\Transformers\\{$model}Transformer";
            $this->transformers[$model] = new $class();
        }
        return $this->transformers[$model];
    }
}
