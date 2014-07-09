<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Extends the {@link http://api.symfony.com/master/Symfony/Component/Form/Extension/Core/Type/FileType.html FileType}
 * by adding an option to mark such a field as a widget for using the
 * {@link http://www.pandastream.com/docs/video_uploader Panda uploader}.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoUploaderExtension extends AbstractTypeExtension
{
    /**
     * The router service
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $router;

    /**
     * General options to configure the display of widget components
     * @var array
     */
    private $defaultOptions;


    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $router         The router
     * @param array                 $defaultOptions Default display options for widget objects
     */
    public function __construct(UrlGeneratorInterface $router, array $defaultOptions)
    {
        $this->router = $router;
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return "file";
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(
            array(
                "panda_widget",
                "panda_widget_version",
                "cloud",
                "multiple_files",
                "cancel_button",
                "progress_bar"
            )
        );
        $resolver->setAllowedValues(
            array("panda_widget_version" => array(1, 2))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options["panda_widget"]) && $options["panda_widget"]) {
            $view->vars["panda_uploader"] = true;

            if (isset($options["panda_widget_version"])) {
                $widgetVersion = $options["panda_widget_version"];
            } else {
                $widgetVersion = 2;
            }

            // generate widgets' ids
            $formId = $view->vars["id"];
            $browseButtonId = "browse_button_$formId";
            $cancelButtonId = "cancel_button_$formId";
            $progressBarId = "progress_bar_$formId";

            // set input field attributes accordingly (widget ids will be
            // read from there by the javascript uploader implementation)
            $view->vars["attr"]["panda-uploader"] = "v$widgetVersion";
            $view->vars["attr"]["authorise-url"] = $this->router->generate(
                "xabbuh_panda_authorise_upload",
                array("cloud" => $options["cloud"])
            );
            if (isset($options["multiple_files"])) {
                $view->vars["attr"]["multiple_files"] = var_export($options["multiple_files"], true);
            } else {
                $view->vars["attr"]["multiple_files"] = var_export($this->defaultOptions["multiple_files"], true);
            }
            $view->vars["attr"]["browse-button-id"] = $browseButtonId;
            $view->vars["attr"]["cancel-button-id"] = $cancelButtonId;
            $view->vars["attr"]["progress-bar-id"] = $progressBarId;

            $view->vars["browse_button_id"] = $browseButtonId;
            $view->vars["browse_button_label"] = "Browse";
            if (isset($options["cancel_button"])) {
                $view->vars["cancel_button"] = $options["cancel_button"];
            } else {
                $view->vars["cancel_button"] = $this->defaultOptions["cancel_button"];
            }
            $view->vars["cancel_button_id"] = $cancelButtonId;
            $view->vars["cancel_button_label"] = "Cancel";
            if (isset($options["progress_bar"])) {
                $view->vars["progress_bar"] = $options["progress_bar"];
            } else {
                $view->vars["progress_bar"] = $this->defaultOptions["progress_bar"];
            }
            $view->vars["progress_bar_id"] = $progressBarId;
        } else {
            $view->vars["panda_uploader"] = false;
        }
    }
}
