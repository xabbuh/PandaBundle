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

use Symfony\Component\Form\FormView;
use Xabbuh\PandaBundle\Form\Extension\VideoUploaderExtension;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoUploaderExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VideoUploaderExtension
     */
    private $extension;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Symfony\Component\Form\FormView
     */
    private $formView;

    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    private $form;

    protected function setUp()
    {
        $this->urlGenerator = $this->createUrlGeneratorMock();
        $this->formView = $this->createFormView();
        $this->form = $this->createFormInterfaceMock();
        $defaultOptions = array(
            'multiple_files' => false,
            'cancel_button' => false,
            'progress_bar' => false,
        );
        $this->extension = new VideoUploaderExtension($this->urlGenerator, $defaultOptions);
    }

    public function testWidgetIsDisabledByDefault()
    {
        $this->buildView(array(), false);

        $this->assertFalse($this->formView->vars['panda_uploader']);
    }

    public function testEnableWidget()
    {
        $this->buildView();

        $this->assertTrue($this->formView->vars['panda_uploader']);
    }

    public function testEnableWidgetWithNonBoolOptionValue()
    {
        $this->buildView(array('panda_widget' => 'yes'));

        $this->assertTrue($this->formView->vars['panda_uploader']);
    }

    public function testIdHasToBeSetToEnableTheWidget()
    {
        $this->buildView(array(), true, null);

        $this->assertFalse($this->formView->vars['panda_uploader']);
    }

    public function testDefaultWidgetVersion()
    {
        $this->buildView(array());

        $this->assertEquals('v2', $this->formView->vars['attr']['panda-uploader']);
    }

    public function testWidgetVersionCanBeChanged()
    {
        $this->buildView(array('panda_widget_version' => 1), true);

        $this->assertEquals('v1', $this->formView->vars['attr']['panda-uploader']);
    }

    public function testDefaultOptions()
    {
        $this->buildView();

        $this->assertEquals('false', $this->formView->vars['attr']['multiple_files']);
        $this->assertFalse($this->formView->vars['cancel_button']);
        $this->assertFalse($this->formView->vars['progress_bar']);
    }

    public function testMultipleFilesUploadsCanBeEnabled()
    {
        $this->buildView(array('multiple_files' => true));

        $this->assertEquals('true', $this->formView->vars['attr']['multiple_files']);
    }

    public function testCancelButtonCanBeEnabled()
    {
        $this->buildView(array('cancel_button' => true));

        $this->assertTrue($this->formView->vars['cancel_button']);
    }

    public function testProgressBarCanBeEnabled()
    {
        $this->buildView(array('progress_bar' => true));

        $this->assertTrue($this->formView->vars['progress_bar']);
    }

    public function testBrowseButtonId()
    {
        $this->buildView(array(), true, 'bar');

        $this->assertEquals('browse_button_bar', $this->formView->vars['attr']['browse-button-id']);
        $this->assertEquals('browse_button_bar', $this->formView->vars['browse_button_id']);
    }

    public function testCancelButtonId()
    {
        $this->buildView(array('cancel_button' => true), true, 'baz');

        $this->assertEquals('cancel_button_baz', $this->formView->vars['attr']['cancel-button-id']);
        $this->assertEquals('cancel_button_baz', $this->formView->vars['cancel_button_id']);
    }

    public function testProgressBarId()
    {
        $this->buildView(array('progress_bar' => true), true, 'foobar');

        $this->assertEquals('progress_bar_foobar', $this->formView->vars['attr']['progress-bar-id']);
        $this->assertEquals('progress_bar_foobar', $this->formView->vars['progress_bar_id']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private function createUrlGeneratorMock()
    {
        return $this
            ->getMockBuilder('\Symfony\Component\Routing\Generator\UrlGeneratorInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Symfony\Component\Form\FormView
     */
    private function createFormView()
    {
        return new FormView();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Form\FormInterface
     */
    private function createFormInterfaceMock()
    {
        return $this->getMock('\Symfony\Component\Form\FormInterface');
    }

    private function buildView(array $options = array(), $enable = true, $id = 'foo')
    {
        if (!isset($options['panda_widget']) && $enable) {
            $options['panda_widget'] = true;
        }

        if (isset($options['panda_widget']) && $options['panda_widget']) {
            $this->formView->vars['id'] = $id;
        }

        $options['cloud'] = 'the-cloud';

        $this->extension->buildView($this->formView, $this->form, $options);
    }
}
