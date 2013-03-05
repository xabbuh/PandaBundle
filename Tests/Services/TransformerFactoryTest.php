<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Tests\Services;

use Xabbuh\PandaBundle\Services\TransformerFactory;

/**
 * Test the TransformerFactory class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class TransformerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that appropriate instances are returned for registered
     * transformer class names.
     */
    public function testGetTransformer()
    {
        $transformerFactory = new TransformerFactory();

        // first, register transformer class names
        $transformerFactory->registerTransformer(
            "Cloud",
            "Xabbuh\\PandaBundle\\Transformers\\CloudTransformer"
        );
        $transformerFactory->registerTransformer(
            "Encoding",
            "Xabbuh\\PandaBundle\\Transformers\\EncodingTransformer"
        );
        $transformerFactory->registerTransformer(
            "Notifications",
            "Xabbuh\\PandaBundle\\Transformers\\NotificationsTransformer"
        );
        $transformerFactory->registerTransformer(
            "Profile",
            "Xabbuh\\PandaBundle\\Transformers\\ProfileTransformer"
        );
        $transformerFactory->registerTransformer(
            "Video",
            "Xabbuh\\PandaBundle\\Transformers\\VideoTransformer"
        );

        // then check that appropriate instances are returned by the factory
        $this->assertTrue(is_object($transformerFactory->get("Cloud")));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\CloudTransformer",
            get_class($transformerFactory->get("Cloud"))
        );
        $this->assertTrue(is_object($transformerFactory->get("Encoding")));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\EncodingTransformer",
            get_class($transformerFactory->get("Encoding"))
        );
        $this->assertTrue(is_object($transformerFactory->get("Notifications")));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\NotificationsTransformer",
            get_class($transformerFactory->get("Notifications"))
        );
        $this->assertTrue(is_object($transformerFactory->get("Profile")));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\ProfileTransformer",
            get_class($transformerFactory->get("Profile"))
        );
        $this->assertTrue(is_object($transformerFactory->get("Video")));
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\VideoTransformer",
            get_class($transformerFactory->get("Video"))
        );
    }

    /**
     * Test that an exception is thrown if a transformer is requested for
     * a model without a registered transformer.
     */
    public function testInvalidArgumentExceptionWhenRetrieving()
    {
        $this->setExpectedException("\\InvalidArgumentException");

        $transformerFactory = new TransformerFactory();
        $transformerFactory->get("Cloud");
    }
}
