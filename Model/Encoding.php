<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Representation of a Video after it was encoded by applying a Profile.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Encoding
{
    private $id;

    private $videoId;

    private $extname;

    private $path;

    private $profileId;

    private $profileName;

    private $status;

    private $encodingProgress;

    private $width;

    private $height;

    private $startedEncodingAt;

    private $encodingTime;

    private $files = array();

    private $createdAt;

    private $updatedAt;


    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getVideoId()
    {
        return $this->videoId;
    }

    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
    }

    public function getExtname()
    {
        return $this->extname;
    }

    public function setExtname($extname)
    {
        $this->extname = $extname;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getProfileId()
    {
        return $this->profileId;
    }

    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    public function getProfileName()
    {
        return $this->profileName;
    }

    public function setProfileName($profileName)
    {
        $this->profileName = $profileName;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getEncodingProgress()
    {
        return $this->encodingProgress;
    }

    public function setEncodingProgress($encodingProgress)
    {
        $this->encodingProgress = $encodingProgress;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getStartedEncodingAt()
    {
        return $this->startedEncodingAt;
    }

    public function setStartedEncodingAt($startedEncodingAt)
    {
        $this->startedEncodingAt = $startedEncodingAt;
    }

    public function getEncodingTime()
    {
        return $this->encodingTime;
    }

    public function setEncodingTime($encodingTime)
    {
        $this->encodingTime = $encodingTime;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function addFile($file)
    {
        $this->files->add($file);
    }

    public function removeFile($file)
    {
        $this->files->removeElement($file);
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
