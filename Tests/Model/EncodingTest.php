<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Tests\Model;

use Xabbuh\PandaBundle\Model\Encoding;

/**
 * Test the Encoding model class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EncodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test getter and setter methods.
     */
    public function testGetterSetter()
    {
        $encoding = new Encoding();
        $encoding->setId("2f8760b7e0d4c7dbe609b5872be9bc3b");
        $encoding->setVideoId("d891d9a45c698d587831466f236c6c6c");
        $encoding->setExtname(".mp4");
        $encoding->setPath("2f8760b7e0d4c7dbe609b5872be9bc3b");
        $encoding->setProfileId("40d9f8711d64aaa74f88462e9274f39a");
        $encoding->setProfileName("h264");
        $encoding->setStatus("success");
        $encoding->setEncodingProgress(99);
        $encoding->setWidth(300);
        $encoding->setHeight(240);
        $encoding->setStartedEncodingAt("2009/10/13 21:28:45 +0000");
        $encoding->setEncodingTime(9000);
        $encoding->addFile("2f8760b7e0d4c7dbe609b5872be9bc3b.mp4");
        $encoding->setCreatedAt("2009/10/13 20:58:29 +0000");
        $encoding->setUpdatedAt("2009/10/13 21:30:34 +0000");

        $this->assertEquals("2f8760b7e0d4c7dbe609b5872be9bc3b", $encoding->getId());
        $this->assertEquals("d891d9a45c698d587831466f236c6c6c", $encoding->getVideoId());
        $this->assertEquals(".mp4", $encoding->getExtname());
        $this->assertEquals("2f8760b7e0d4c7dbe609b5872be9bc3b", $encoding->getPath());
        $this->assertEquals("40d9f8711d64aaa74f88462e9274f39a", $encoding->getProfileId());
        $this->assertEquals("h264", $encoding->getProfileName());
        $this->assertEquals("success", $encoding->getStatus());
        $this->assertEquals(99, $encoding->getEncodingProgress());
        $this->assertEquals(300, $encoding->getWidth());
        $this->assertEquals(240, $encoding->getHeight());
        $this->assertEquals("2009/10/13 21:28:45 +0000", $encoding->getStartedEncodingAt());
        $this->assertEquals(9000, $encoding->getEncodingTime());
        $this->assertEquals(
            array("2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"),
            $encoding->getFiles()->getValues()
        );
        $this->assertEquals("2009/10/13 20:58:29 +0000", $encoding->getCreatedAt());
        $this->assertEquals("2009/10/13 21:30:34 +0000", $encoding->getUpdatedAt());

        // test the removal of files
        $encoding->removeFile("40d9f8711d64aaa74f88462e9274f39a.mp4");
        $this->assertEquals(
            array("2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"),
            $encoding->getFiles()->getValues()
        );
        $encoding->removeFile("2f8760b7e0d4c7dbe609b5872be9bc3b.mp4");
        $this->assertEquals(array(), $encoding->getFiles()->getValues());
    }
}
