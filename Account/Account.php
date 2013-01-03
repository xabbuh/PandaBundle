<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Account;

/**
 * Represenation of a panda account.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Account
{
    private $accessKey;

    private $secretKey;

    private $apiHost;

    public function __construct($accessKey, $secretKey, $apiHost)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->apiHost = $apiHost;
    }

    public function getAccessKey()
    {
        return $this->accessKey;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function getApiHost()
    {
        return $this->apiHost;
    }
}
