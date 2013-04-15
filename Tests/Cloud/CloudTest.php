<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Tests\Cloud;

use Xabbuh\PandaBundle\Cloud\Cloud;
use Xabbuh\PandaBundle\Model\NotificationEvent;
use Xabbuh\PandaBundle\Model\Notifications;
use Xabbuh\PandaBundle\Services\TransformerFactory;
use Xabbuh\PandaBundle\Transformers\CloudTransformer;
use Xabbuh\PandaBundle\Transformers\EncodingTransformer;
use Xabbuh\PandaBundle\Transformers\NotificationsTransformer;
use Xabbuh\PandaBundle\Transformers\ProfileTransformer;
use Xabbuh\PandaBundle\Transformers\VideoTransformer;

/**
 * Tests for the Cloud class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Xabbuh\PandaClient\ApiInterface
     */
    protected $api;

    /**
     * @var \Xabbuh\PandaBundle\Services\TransformerFactory
     */
    protected static $transformerFactory;

    /**
     * The Cloud implementation being tested
     * @var \Xabbuh\PandaBundle\Cloud\Cloud
     */
    protected $cloud;


    public static function setUpBeforeClass()
    {
        self::$transformerFactory = new TransformerFactory();
        self::$transformerFactory->registerTransformer(
            "Cloud",
            new CloudTransformer()
        );
        self::$transformerFactory->registerTransformer(
            "Encoding",
            new EncodingTransformer()
        );
        self::$transformerFactory->registerTransformer(
            "Notifications",
            new NotificationsTransformer()
        );
        self::$transformerFactory->registerTransformer(
            "Profile",
            new ProfileTransformer()
        );
        self::$transformerFactory->registerTransformer(
            "Video",
            new VideoTransformer()
        );
    }

    protected function setUp()
    {
        $restClientMock = $this->getMock("Xabbuh\\PandaClient\\RestClientInterface");
        $this->api =  $this->getMock("Xabbuh\\PandaClient\\ApiInterface");
        $this->api->expects($this->any())
            ->method("getRestClient")
            ->will($this->returnValue($restClientMock));
        $this->cloud = new Cloud($this->api, self::$transformerFactory);
    }

    public function testGetPandaApi()
    {
        $this->assertEquals($this->api, $this->cloud->getPandaApi());
    }

    public function testGetVideos()
    {
        $returnValue = '[{
          "id":"d891d9a45c698d587831466f236c6c6c",
          "original_filename":"test.mp4",
          "extname":".mp4",
          "path":"d891d9a45c698d587831466f236c6c6c",
          "video_codec":"h264",
          "audio_codec":"aac",
          "height":240,
          "width":300,
          "fps":29,
          "duration":14000,
          "file_size": 39458349,
          "created_at":"2009/10/13 19:11:26 +0100",
          "updated_at":"2009/10/13 19:11:26 +0100"
        },
        {
          "id":"130466751aaaac1f88eb7e31c93ce40c",
          "source_url": "http://example.com/test2.mp4",
          "extname":".mp4",
          "path":"130466751aaaac1f88eb7e31c93ce40c",
          "video_codec":"h264",
          "audio_codec":"aac",
          "height":640,
          "width":360
        }]';
        $this->api->expects($this->once())
            ->method("getVideos")
            ->will($this->returnValue($returnValue));
        $collection = $this->cloud->getVideos();
        $this->assertTrue(is_array($collection));
        $this->assertEquals(2, count($collection));
        foreach ($collection as $video) {
            $this->assertEquals("Xabbuh\\PandaBundle\Model\\Video", get_class($video));
        }
    }

    public function testGetVideosForPaginationWithDefaultParameters()
    {
        $returnValue = '{ "videos": [{
              "id":"d891d9a45c698d587831466f236c6c6c",
              "original_filename":"test.mp4",
              "extname":".mp4",
              "path":"d891d9a45c698d587831466f236c6c6c",
              "video_codec":"h264",
              "audio_codec":"aac",
              "height":240,
              "width":300,
              "fps":29,
              "duration":14000,
              "file_size": 39458349,
              "created_at":"2009/10/13 19:11:26 +0100",
              "updated_at":"2009/10/13 19:11:26 +0100"
            },
            {
              "id":"130466751aaaac1f88eb7e31c93ce40c",
              "source_url": "http://example.com/test2.mp4",
              "extname":".mp4",
              "path":"130466751aaaac1f88eb7e31c93ce40c",
              "video_codec":"h264",
              "audio_codec":"aac",
              "height":640,
              "width":360
            }],
        "page": 1,
        "per_page": 100,
        "total": 17
        }';
        $this->api->expects($this->once())
            ->method("getVideosForPagination")
            ->will($this->returnValue($returnValue));
        $result = $this->cloud->getVideosForPagination();
        $this->assertTrue(is_object($result));
        $this->assertEquals(1, $result->page);
        $this->assertEquals(100, $result->per_page);
        $this->assertEquals(17, $result->total);
        $this->assertTrue(is_array($result->videos));
        $this->assertEquals(2, count($result->videos));
        foreach ($result->videos as $video) {
            $this->assertEquals("Xabbuh\\PandaBundle\Model\\Video", get_class($video));
        }
    }

    public function testGetVideosForPaginationWithPageParameter()
    {
        $returnValue = '{ "videos": [{
              "id":"d891d9a45c698d587831466f236c6c6c",
              "original_filename":"test.mp4",
              "extname":".mp4",
              "path":"d891d9a45c698d587831466f236c6c6c",
              "video_codec":"h264",
              "audio_codec":"aac",
              "height":240,
              "width":300,
              "fps":29,
              "duration":14000,
              "file_size": 39458349,
              "created_at":"2009/10/13 19:11:26 +0100",
              "updated_at":"2009/10/13 19:11:26 +0100"
            },
            {
              "id":"130466751aaaac1f88eb7e31c93ce40c",
              "source_url": "http://example.com/test2.mp4",
              "extname":".mp4",
              "path":"130466751aaaac1f88eb7e31c93ce40c",
              "video_codec":"h264",
              "audio_codec":"aac",
              "height":640,
              "width":360
            }],
        "page": 5,
        "per_page": 100,
        "total": 17
        }';
        $this->api->expects($this->once())
            ->method("getVideosForPagination")
            ->with($this->equalTo(5))
            ->will($this->returnValue($returnValue));
        $result = $this->cloud->getVideosForPagination(5);
        $this->assertTrue(is_object($result));
        $this->assertEquals(5, $result->page);
        $this->assertEquals(100, $result->per_page);
        $this->assertEquals(17, $result->total);
        $this->assertTrue(is_array($result->videos));
        $this->assertEquals(2, count($result->videos));
        foreach ($result->videos as $video) {
            $this->assertEquals("Xabbuh\\PandaBundle\Model\\Video", get_class($video));
        }
    }

    public function testGetVideosForPaginationWithPageAndPerPageParameters()
    {
        $returnValue = '{ "videos": [{
              "id":"d891d9a45c698d587831466f236c6c6c",
              "original_filename":"test.mp4",
              "extname":".mp4",
              "path":"d891d9a45c698d587831466f236c6c6c",
              "video_codec":"h264",
              "audio_codec":"aac",
              "height":240,
              "width":300,
              "fps":29,
              "duration":14000,
              "file_size": 39458349,
              "created_at":"2009/10/13 19:11:26 +0100",
              "updated_at":"2009/10/13 19:11:26 +0100"
            },
            {
              "id":"130466751aaaac1f88eb7e31c93ce40c",
              "source_url": "http://example.com/test2.mp4",
              "extname":".mp4",
              "path":"130466751aaaac1f88eb7e31c93ce40c",
              "video_codec":"h264",
              "audio_codec":"aac",
              "height":640,
              "width":360
            }],
        "page": 7,
        "per_page": 25,
        "total": 17
        }';
        $this->api->expects($this->once())
            ->method("getVideosForPagination")
            ->with(
                $this->equalTo(7),
                $this->equalTo(25)
            )
            ->will($this->returnValue($returnValue));
        $result = $this->cloud->getVideosForPagination(7, 25);
        $this->assertTrue(is_object($result));
        $this->assertEquals(7, $result->page);
        $this->assertEquals(25, $result->per_page);
        $this->assertEquals(17, $result->total);
        $this->assertTrue(is_array($result->videos));
        $this->assertEquals(2, count($result->videos));
        foreach ($result->videos as $video) {
            $this->assertEquals("Xabbuh\\PandaBundle\Model\\Video", get_class($video));
        }
    }

    public function testDeleteVideo()
    {
        $videoId = md5(uniqid());
        $this->api->expects($this->once())
            ->method("deleteVideo")
            ->with($this->equalTo($videoId))
            ->will($this->returnValue("status: 200"))
        ;
        $this->assertEquals("status: 200", $this->cloud->deleteVideo($videoId));
    }

    public function testEncodeVideoByUrl()
    {
        $url = "http://www.example.com/video.mp4";
        $returnValue = '{
          "id":"d891d9a45c698d587831466f236c6c6c",
          "original_filename":"video.mp4",
          "extname":".mp4",
          "path":"d891d9a45c698d587831466f236c6c6c",
          "video_codec":"h264",
          "audio_codec":"aac",
          "height":240,
          "width":300,
          "fps":29,
          "duration":14000,
          "file_size":39458349,
          "created_at":"2009/10/13 19:11:26 +0100",
          "updated_at":"2009/10/13 19:11:26 +0100"
        }';
        $this->api->expects($this->once())
            ->method("encodeVideoByUrl")
            ->with($this->equalTo($url))
            ->will($this->returnValue($returnValue));
        $video = $this->cloud->encodeVideoByUrl($url);
        $this->assertEquals("d891d9a45c698d587831466f236c6c6c", $video->getId());
        $this->assertEquals("video.mp4", $video->getOriginalFilename());
        $this->assertEquals(".mp4", $video->getExtname());
        $this->assertEquals("d891d9a45c698d587831466f236c6c6c", $video->getPath());
        $this->assertEquals("h264", $video->getVideoCodec());
        $this->assertEquals("aac", $video->getAudioCodec());
        $this->assertEquals("240", $video->getHeight());
        $this->assertEquals("300", $video->getWidth());
        $this->assertEquals("29", $video->getFps());
        $this->assertEquals("14000", $video->getDuration());
        $this->assertEquals("39458349", $video->getFileSize());
        $this->assertEquals("2009/10/13 19:11:26 +0100", $video->getCreatedAt());
        $this->assertEquals("2009/10/13 19:11:26 +0100", $video->getUpdatedAt());
    }

    public function testEncodeVideoFile()
    {
        $filename = "video.mp4";
        $returnValue = '{
          "id":"d891d9a45c698d587831466f236c6c6c",
          "original_filename":"video.mp4",
          "extname":".mp4",
          "path":"d891d9a45c698d587831466f236c6c6c",
          "video_codec":"h264",
          "audio_codec":"aac",
          "height":240,
          "width":300,
          "fps":29,
          "duration":14000,
          "file_size":39458349,
          "created_at":"2009/10/13 19:11:26 +0100",
          "updated_at":"2009/10/13 19:11:26 +0100"
        }';
        $this->api->expects($this->once())
            ->method("encodeVideoFile")
            ->with($this->equalTo($filename))
            ->will($this->returnValue($returnValue));
        $video = $this->cloud->encodeVideoFile($filename);
        $this->assertEquals("d891d9a45c698d587831466f236c6c6c", $video->getId());
        $this->assertEquals("video.mp4", $video->getOriginalFilename());
        $this->assertEquals(".mp4", $video->getExtname());
        $this->assertEquals("d891d9a45c698d587831466f236c6c6c", $video->getPath());
        $this->assertEquals("h264", $video->getVideoCodec());
        $this->assertEquals("aac", $video->getAudioCodec());
        $this->assertEquals("240", $video->getHeight());
        $this->assertEquals("300", $video->getWidth());
        $this->assertEquals("29", $video->getFps());
        $this->assertEquals("14000", $video->getDuration());
        $this->assertEquals("39458349", $video->getFileSize());
        $this->assertEquals("2009/10/13 19:11:26 +0100", $video->getCreatedAt());
        $this->assertEquals("2009/10/13 19:11:26 +0100", $video->getUpdatedAt());
    }

    public function testRegisterUpload()
    {
        $id = md5(uniqid());
        $location = "http://example.com/$id";
        $filename = "video.mp4";
        $filesize = 114373213;
        $returnValue = sprintf(
            '{ "id": "%s", "location": "%s" }',
            $id,
            $location
        );
        $this->api->expects($this->once())
            ->method("registerUpload")
            ->with(
                $this->equalTo($filename),
                $this->equalTo($filesize)
            )
            ->will($this->returnValue($returnValue));
        $upload = $this->cloud->registerUpload($filename, $filesize);
        $this->assertEquals($id, $upload->id);
        $this->assertEquals($location, $upload->location);
    }

    public function testRegisterUploadWithProfilesList()
    {
        $id = md5(uniqid());
        $location = "http://example.com/$id";
        $filename = "video.mpg";
        $filesize = 114373213;
        $returnValue = sprintf(
            '{ "id": "%s", "location": "%s" }',
            $id,
            $location
        );
        $profiles = array("profile1", "profile2");
        $this->api->expects($this->once())
            ->method("registerUpload")
            ->with(
                $this->equalTo($filename),
                $this->equalTo($filesize),
                $this->equalTo($profiles)
            )
            ->will($this->returnValue($returnValue));
        $upload = $this->cloud->registerUpload($filename, $filesize, $profiles);
        $this->assertEquals($id, $upload->id);
        $this->assertEquals($location, $upload->location);
    }

    public function testRegisterUploadWithAllProfiles()
    {
        $id = md5(uniqid());
        $location = "http://example.com/$id";
        $filename = "video.mpg";
        $filesize = 114373213;
        $returnValue = sprintf(
            '{ "id": "%s", "location": "%s" }',
            $id,
            $location
        );
        $this->api->expects($this->once())
            ->method("registerUpload")
            ->with(
                $this->equalTo($filename),
                $this->equalTo($filesize),
                $this->isNull(),
                $this->isTrue()
            )
            ->will($this->returnValue($returnValue));
        $upload = $this->cloud->registerUpload($filename, $filesize, null, true);
        $this->assertEquals($id, $upload->id);
        $this->assertEquals($location, $upload->location);
    }

    public function testGetEncodings()
    {
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->once())
            ->method("getEncodings")
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodings();
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsWithFilter()
    {
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->once())
            ->method("getEncodings")
            ->with(
                $this->equalTo(array("status" => "success"))
            )
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodings(array("status" => "success"));
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsWithStatus()
    {
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with($this->equalTo(array("status" => "success")))
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("getEncodingsWithStatus")
            ->with($this->equalTo("success"))
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsWithStatus("success");
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsWithStatusAndFilter()
    {
        $videoId = md5(uniqid());
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with(
                $this->equalTo(
                    array("status" => "success", "video_id" => $videoId)
                )
            )
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("getEncodingsWithStatus")
            ->with(
                $this->equalTo("success"),
                $this->equalTo(
                    array("video_id" => $videoId)
                )
            )
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsWithStatus(
            "success",
            array("video_id" => $videoId)
        );
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsForProfile()
    {
        $profileId = md5(uniqid());
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with(
                $this->equalTo(array("profile_id" => $profileId))
            )
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("getEncodingsForProfile")
            ->with($this->equalTo($profileId))
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsForProfile($profileId);
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsForProfileWithFilter()
    {
        $profileId = md5(uniqid());
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with(
                $this->equalTo(array("status" => "success", "profile_id" => $profileId))
            )
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("getEncodingsForProfile")
            ->with(
                $this->equalTo($profileId),
                $this->equalTo(array("status" => "success"))
            )
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsForProfile(
            $profileId,
            array("status" => "success")
        );
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsForProfileByName()
    {
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with(
                $this->equalTo(array("profile_name" => "h264"))
            )
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("get")
            ->with($this->equalTo("h264"))
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsForProfileByName("h264");
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsForProfileByNameWithFilter()
    {
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with(
                $this->equalTo(
                    array("profile_name" => "h264", "status" => "success")
                )
            )
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("getEncodingsForProfileByName")
            ->with(
                $this->equalTo("h264"),
                $this->equalTo(array("status" => "success"))
            )
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsForProfileByName(
            "h264",
            array("status" => "success")
        );
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsForVideo()
    {
        $videoId = md5(uniqid());
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with($this->equalTo(array("video_id" => $videoId)))
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("getEncodingsForVideo")
            ->with($this->equalTo($videoId))
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsForVideo($videoId);
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetEncodingsForVideoWithFilter()
    {
        $videoId = md5(uniqid());
        $returnValue = '[{
          "id":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "video_id":"d891d9a45c698d587831466f236c6c6c",
          "extname":".mp4",
          "path":"2f8760b7e0d4c7dbe609b5872be9bc3b",
          "profile_id":"40d9f8711d64aaa74f88462e9274f39a",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":99,
          "height":240,
          "width":300,
          "started_encoding_at":"2009/10/13 21:28:45 +0000",
          "encoding_time":9000,
          "files":["2f8760b7e0d4c7dbe609b5872be9bc3b.mp4"],
          "created_at":"2009/10/13 20:58:29 +0000",
          "updated_at":"2009/10/13 21:30:34 +0000"
        },
        {
          "id":"ab658d9599ca70966cfd0f53c186712b",
          "video_id":"b7e67ca5c92f381f7fd9ce341e6609c6",
          "extname":".mp4",
          "path":"ab658d9599ca70966cfd0f53c186712b",
          "profile_id":"ab658d9599ca70966cfd0f53c186712b",
          "profile_name":"h264",
          "status":"success",
          "encoding_progress":50,
          "height":240,
          "width":300,
          "files":["ab658d9599ca70966cfd0f53c186712b.mp4"],
          "created_at":"2011/1/31 10:39:13 +0000",
          "updated_at":"2012/12/5 01:49:27 +0000"
        }]';
        $this->api->expects($this->any())
            ->method("getEncodings")
            ->with($this->equalTo(
                array("status" => "success", "video_id" => $videoId))
            )
            ->will($this->returnValue($returnValue));
        $this->api->expects($this->any())
            ->method("getEncodingsForVideo")
            ->with(
                $this->equalTo($videoId),
                $this->equalTo(array("status" => "success"))
            )
            ->will($this->returnValue($returnValue));
        $encodings = $this->cloud->getEncodingsForVideo(
            $videoId,
            array("status" => "success")
        );
        $this->assertTrue(is_array($encodings));
        $this->assertEquals(2, count($encodings));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[0])
        );
        $this->assertEquals(
            "2f8760b7e0d4c7dbe609b5872be9bc3b",
            $encodings[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Encoding",
            get_class($encodings[1])
        );
        $this->assertEquals(
            "ab658d9599ca70966cfd0f53c186712b",
            $encodings[1]->getId()
        );
    }

    public function testGetProfiles()
    {
        $returnValue = '[{
           "id":"40d9f8711d64aaa74f88462e9274f39a",
           "title":"MP4 (H.264)",
           "name": "h264",
           "extname":".mp4",
           "width":320,
           "height":240,
           "audio_bitrate": 128,
           "video_bitrate": 500,
           "aspect_mode": "letterbox",
           "command":"ffmpeg -i $input_file$ -c:a libfaac $audio_bitrate$ -c:v libx264 $video_bitrate$ -preset medium $filters$ -y $output_file$",
           "created_at":"2009/10/14 18:36:30 +0000",
           "updated_at":"2009/10/14 19:38:42 +0000"
        },
        {
           "id":"c629221e4595928f7f6838bfd30469c3",
           "title":"WebM (VP8)",
           "name": "vp8",
           "extname":".webm",
           "width":640,
           "height":480,
           "audio_bitrate": 128,
           "video_bitrate": "auto",
           "aspect_mode": "letterbox",
           "created_at":"2013/02/06 11:02:49 +0000",
           "updated_at":"2013/02/06 11:02:49 +0000"
        }]';
        $this->api->expects($this->once())
            ->method("getProfiles")
            ->will($this->returnValue($returnValue));
        $profiles = $this->cloud->getProfiles();
        $this->assertTrue(is_array($profiles));
        $this->assertEquals(2, count($profiles));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Profile",
            get_class($profiles[0])
        );
        $this->assertEquals(
            "40d9f8711d64aaa74f88462e9274f39a",
            $profiles[0]->getId()
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Profile",
            get_class($profiles[1])
        );
        $this->assertEquals(
            "c629221e4595928f7f6838bfd30469c3",
            $profiles[1]->getId()
        );
    }

    public function testGetProfile()
    {
        $id = md5(uniqid());
        $returnValue = '{
           "id":"40d9f8711d64aaa74f88462e9274f39a",
           "title":"MP4 (H.264)",
           "name": "h264",
           "extname":".mp4",
           "width":320,
           "height":240,
           "audio_bitrate": 128,
           "video_bitrate": 500,
           "aspect_mode": "letterbox",
           "command":"ffmpeg -i $input_file$ -c:a libfaac $audio_bitrate$ -c:v libx264 $video_bitrate$ -preset medium $filters$ -y $output_file$",
           "created_at":"2009/10/14 18:36:30 +0000",
           "updated_at":"2009/10/14 19:38:42 +0000"
        }';
        $this->api->expects($this->once())
            ->method("getProfile")
            ->with($this->equalTo($id))
            ->will($this->returnValue($returnValue));
        $profile = $this->cloud->getProfile($id);
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Profile",
            get_class($profile)
        );
        $this->assertEquals("40d9f8711d64aaa74f88462e9274f39a", $profile->getId());
        $this->assertEquals("MP4 (H.264)", $profile->getTitle());
        $this->assertEquals("h264", $profile->getName());
        $this->assertEquals(".mp4", $profile->getExtname());
        $this->assertEquals(320, $profile->getWidth());
        $this->assertEquals(240, $profile->getHeight());
        $this->assertEquals(128, $profile->getAudioBitrate());
        $this->assertEquals(500, $profile->getVideoBitrate());
        $this->assertEquals("letterbox", $profile->getAspectMode());
        $this->assertEquals(
            "ffmpeg -i \$input_file\$ -c:a libfaac \$audio_bitrate\$ -c:v libx264 \$video_bitrate\$ -preset medium \$filters\$ -y \$output_file\$",
            $profile->getCommand()
        );
        $this->assertEquals("2009/10/14 18:36:30 +0000", $profile->getCreatedAt());
        $this->assertEquals("2009/10/14 19:38:42 +0000", $profile->getUpdatedAt());
    }

    public function testGetCloudData()
    {
        $id = md5(uniqid());
        $returnValue = '{
          "id": "e122090f4e506ae9ee266c3eb78a8b67",
          "name": "my_first_cloud",
          "s3_videos_bucket": "my-example-bucket",
          "s3_private_access":false,
          "url": "http://my-example-bucket.s3.amazonaws.com/",
          "created_at": "2010/03/18 12:56:04 +0000",
          "updated_at": "2010/03/18 12:59:06 +0000"
        }';

        $restClientMock = $this->api->getRestClient();
        $restClientMock->expects($this->once())
            ->method("getCloudId")
            ->will($this->returnValue($id));

        $this->api->expects($this->once())
            ->method("getCloud")
            ->will($this->returnValue($returnValue));

        $cloud = $this->cloud->getCloudData();
        $this->assertEquals("e122090f4e506ae9ee266c3eb78a8b67", $cloud->getId());
        $this->assertEquals("my_first_cloud", $cloud->getName());
        $this->assertEquals("my-example-bucket", $cloud->getS3VideosBucket());
        $this->assertEquals(false, $cloud->isS3AccessPrivate());
        $this->assertEquals("http://my-example-bucket.s3.amazonaws.com/", $cloud->getUrl());
        $this->assertEquals("2010/03/18 12:56:04 +0000", $cloud->getCreatedAt());
        $this->assertEquals("2010/03/18 12:59:06 +0000", $cloud->getUpdatedAt());
    }

    public function testSetCloud()
    {
        $id = md5(uniqid());
        $data = array(
            "name" => "my_first_cloud",
            "s3_videos_bucket" => "my_own_bucket",
            "aws_access_key" => "XQwEwFR",
            "aws_secret_key" => "XoSV2f"
        );
        $returnValue = '{
          "id": "e122090f4e506ae9ee266c3eb78a8b67",
          "name": "my_first_cloud",
          "s3_videos_bucket": "my-example-bucket",
          "s3_private_access":false,
          "url": "http://my-example-bucket.s3.amazonaws.com/",
          "created_at": "2010/03/18 12:56:04 +0000",
          "updated_at": "2010/03/18 12:59:06 +0000"
        }';
        $this->api->expects($this->once())
            ->method("setCloud")
            ->with(
                $this->equalTo($id),
                $this->equalTo($data)
            )
            ->will($this->returnValue($returnValue));
        $cloud = $this->cloud->setCloud($id, $data);
        $this->assertEquals("e122090f4e506ae9ee266c3eb78a8b67", $cloud->getId());
        $this->assertEquals("my_first_cloud", $cloud->getName());
        $this->assertEquals("my-example-bucket", $cloud->getS3VideosBucket());
        $this->assertEquals(false, $cloud->isS3AccessPrivate());
        $this->assertEquals("http://my-example-bucket.s3.amazonaws.com/", $cloud->getUrl());
        $this->assertEquals("2010/03/18 12:56:04 +0000", $cloud->getCreatedAt());
        $this->assertEquals("2010/03/18 12:59:06 +0000", $cloud->getUpdatedAt());
    }

    public function testGetNotifications()
    {
        $returnValue = '{
          "url": null,
          "events": {
            "video_created": false,
            "video_encoded": false,
            "encoding_progress": false,
            "encoding_completed": false
          }
        }';
        $this->api->expects($this->once())
            ->method("getNotifications")
            ->will($this->returnValue($returnValue));
        $notifications = $this->cloud->getNotifications();
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Notifications",
            get_class($notifications)
        );
        $this->assertNull($notifications->getUrl());
        $videoCreatedEvent = $notifications->getNotificationEvent("video-created");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($videoCreatedEvent)
        );
        $this->assertFalse($videoCreatedEvent->isActive());
        $videoEncodedEvent = $notifications->getNotificationEvent("video-encoded");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($videoEncodedEvent)
        );
        $this->assertFalse($videoEncodedEvent->isActive());
        $encodingProgressEvent = $notifications->getNotificationEvent("encoding-progress");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($encodingProgressEvent)
        );
        $this->assertFalse($encodingProgressEvent->isActive());
        $encodingCompletedEvent = $notifications->getNotificationEvent("encoding-completed");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($encodingCompletedEvent)
        );
        $this->assertFalse($encodingCompletedEvent->isActive());
    }

    public function testSetNotifications()
    {
        // the new notifications configuration
        $notifications = new Notifications();
        $notifications->setUrl("http://example.com/panda_notification");
        $notifications->addNotificationEvent(
            new NotificationEvent("video-created", false)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent("video-encoded", true)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent("encoding-progress", false)
        );
        $notifications->addNotificationEvent(
            new NotificationEvent("encoding-completed", false)
        );

        // what will be passed to the underlying api call
        $data = array(
            "url" => "http://example.com/panda_notification",
            "events[video_created]" => "false",
            "events[video_encoded]" => "true",
            "events[encoding_progress]" => "false",
            "events[encoding_completed]" => "false"
        );

        // what will be returned by the underlying api call
        $returnValue = '{
          "url": "http://example.com/panda_notification",
          "events": {
            "video_created": false,
            "video_encoded": true,
            "encoding_progress": false,
            "encoding_completed": false
          }
        }';
        $this->api->expects($this->once())
            ->method("setNotifications")
            ->with($this->equalTo($data))
            ->will($this->returnValue($returnValue));
        $notifications = $this->cloud->setNotifications($notifications);
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\Notifications",
            get_class($notifications)
        );
        $this->assertEquals(
            "http://example.com/panda_notification",
            $notifications->getUrl()
        );
        $videoCreatedEvent = $notifications->getNotificationEvent("video-created");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($videoCreatedEvent)
        );
        $this->assertFalse($videoCreatedEvent->isActive());
        $videoEncodedEvent = $notifications->getNotificationEvent("video-encoded");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($videoEncodedEvent)
        );
        $this->assertTrue($videoEncodedEvent->isActive());
        $encodingProgressEvent = $notifications->getNotificationEvent("encoding-progress");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($encodingProgressEvent)
        );
        $this->assertFalse($encodingProgressEvent->isActive());
        $encodingCompletedEvent = $notifications->getNotificationEvent("encoding-completed");
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Model\\NotificationEvent",
            get_class($encodingCompletedEvent)
        );
        $this->assertFalse($encodingCompletedEvent->isActive());
    }
}
