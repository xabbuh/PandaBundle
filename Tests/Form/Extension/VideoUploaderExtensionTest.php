<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Tests\Form\Extension;

use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Xabbuh\PandaBundle\Form\Extension\VideoUploaderExtension;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoUploaderExtensionTest extends FormIntegrationTestCase
{
    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    protected function setUp()
    {
        $this->urlGenerator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')->getMock();

        parent::setUp();
    }

    protected function getExtensions()
    {
        $defaultOptions = array(
            'multiple_files' => false,
            'cancel_button' => false,
            'progress_bar' => false,
        );
        $extension = new VideoUploaderExtension($this->urlGenerator, $defaultOptions);

        return array(
            new PreloadedExtension(array(), array($extension->getExtendedType() => array($extension))),
        );
    }

    public function testWidgetIsDisabledByDefault()
    {
        $formView = $this->buildView(array(), false);

        $this->assertFalse($formView->vars['panda_uploader']);
    }

    public function testEnableWidget()
    {
        $formView = $this->buildView();

        $this->assertTrue($formView->vars['panda_uploader']);
    }

    public function testEnableWidgetWithNonBoolOptionValue()
    {
        $formView = $this->buildView(array('panda_widget' => 'yes'));

        $this->assertTrue($formView->vars['panda_uploader']);
    }

    public function testDefaultWidgetVersion()
    {
        $formView = $this->buildView(array());

        $this->assertEquals('v2', $formView->vars['attr']['panda-uploader']);
    }

    public function testWidgetVersionCanBeChanged()
    {
        $formView = $this->buildView(array('panda_widget_version' => 1), true);

        $this->assertEquals('v1', $formView->vars['attr']['panda-uploader']);
    }

    public function testDefaultOptions()
    {
        $formView = $this->buildView();

        $this->assertEquals('false', $formView->vars['attr']['multiple_files']);
        $this->assertFalse($formView->vars['cancel_button']);
        $this->assertFalse($formView->vars['progress_bar']);
    }

    public function testMultipleFilesUploadsCanBeEnabled()
    {
        $formView = $this->buildView(array('multiple_files' => true));

        $this->assertEquals('true', $formView->vars['attr']['multiple_files']);
    }

    public function testCancelButtonCanBeEnabled()
    {
        $formView = $this->buildView(array('cancel_button' => true));

        $this->assertTrue($formView->vars['cancel_button']);
    }

    public function testProgressBarCanBeEnabled()
    {
        $formView = $this->buildView(array('progress_bar' => true));

        $this->assertTrue($formView->vars['progress_bar']);
    }

    public function testBrowseButtonId()
    {
        $formView = $this->buildView(array(), true, 'bar');

        $this->assertEquals('browse_button_bar', $formView->vars['attr']['browse-button-id']);
        $this->assertEquals('browse_button_bar', $formView->vars['browse_button_id']);
    }

    public function testCancelButtonId()
    {
        $formView = $this->buildView(array('cancel_button' => true), true, 'baz');

        $this->assertEquals('cancel_button_baz', $formView->vars['attr']['cancel-button-id']);
        $this->assertEquals('cancel_button_baz', $formView->vars['cancel_button_id']);
    }

    public function testProgressBarId()
    {
        $formView = $this->buildView(array('progress_bar' => true), true, 'foobar');

        $this->assertEquals('progress_bar_foobar', $formView->vars['attr']['progress-bar-id']);
        $this->assertEquals('progress_bar_foobar', $formView->vars['progress_bar_id']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @dataProvider invalidOptionsProvider
     */
    public function testExceptionIsThrownForInvalidOptions(array $options)
    {
        $this->factory->create('Symfony\Component\Form\Extension\Core\Type\FileType', null, $options);
    }

    public function invalidOptionsProvider()
    {
        return array(
            'widget-without-cloud' => array(array('panda_widget' => true)),
            'multiple-files-not-boolean' => array(array('multiple_files' => 'false')),
            'cancel-button-not-boolean' => array(array('cancel_button' => 'true')),
            'panda-widget-not-boolean' => array(array('panda_widget' => 'false')),
            'progress-bar-not-boolean' => array(array('progress_bar' => 'true')),
        );
    }

    private function buildView(array $options = array(), $enable = true, $id = 'foo')
    {
        if (!isset($options['panda_widget']) && $enable) {
            $options['panda_widget'] = true;
        }

        $options['cloud'] = 'the-cloud';

        $form = $this->factory->createNamed($id, 'Symfony\Component\Form\Extension\Core\Type\FileType', null, $options);

        return $form->createView();
    }
}
