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
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     * The url generator
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * General options to configure the display of widget components
     * @var array
     */
    private $defaultOptions;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator   The url generator
     * @param array                 $defaultOptions Default display options for widget objects
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, array $defaultOptions)
    {
        $this->urlGenerator = $urlGenerator;
        $this->defaultOptions = array_replace(array(
            'multiple_files' => false,
            'cancel_button' => true,
            'progress_bar' => true,
        ), $defaultOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FileType';
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'panda_widget' => false,
            'panda_widget_version' => 2,
            'cloud' => null,
        ));
        $resolver->setDefaults($this->defaultOptions);

        $cloudNormalizer = function (Options $options, $cloud) {
            if (!$options['panda_widget']) {
                return null;
            }

            if (null === $cloud) {
                throw new InvalidOptionsException('The "cloud" option is required when enabling the panda widget.');
            }

            return $cloud;
        };

        $resolver->setNormalizer('cloud', $cloudNormalizer);
        $resolver->setAllowedValues('panda_widget', array(true, false, 'yes', 'no'));
        $resolver->setAllowedValues('panda_widget_version', array(1, 2));
        $resolver->setAllowedTypes('cancel_button', 'bool');
        $resolver->setAllowedTypes('cloud', array('null', 'string'));
        $resolver->setAllowedTypes('multiple_files', 'bool');
        $resolver->setAllowedTypes('panda_widget', array('bool', 'string'));
        $resolver->setAllowedTypes('progress_bar', 'bool');
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$options['panda_widget'] || !isset($view->vars['id'])) {
            $view->vars["panda_uploader"] = false;

            return;
        }

        $view->vars["panda_uploader"] = true;

        $widgetVersion = $options["panda_widget_version"];

        // generate widgets' ids
        $formId = $view->vars["id"];
        $browseButtonId = "browse_button_$formId";
        $cancelButtonId = "cancel_button_$formId";
        $progressBarId = "progress_bar_$formId";

        // set input field attributes accordingly (widget ids will be
        // read from there by the javascript uploader implementation)
        $view->vars["attr"]["panda-uploader"] = "v$widgetVersion";
        $view->vars["attr"]["authorise-url"] = $this->urlGenerator->generate(
            "xabbuh_panda_authorise_upload",
            array("cloud" => $options["cloud"])
        );
        $view->vars["attr"]["multiple_files"] = var_export($options["multiple_files"], true);
        $view->vars["attr"]["browse-button-id"] = $browseButtonId;
        $view->vars["attr"]["cancel-button-id"] = $cancelButtonId;
        $view->vars["attr"]["progress-bar-id"] = $progressBarId;

        $view->vars["browse_button_id"] = $browseButtonId;
        $view->vars["browse_button_label"] = "Browse";
        $view->vars["cancel_button"] = $options["cancel_button"];
        $view->vars["cancel_button_id"] = $cancelButtonId;
        $view->vars["cancel_button_label"] = "Cancel";
        $view->vars["progress_bar"] = $options["progress_bar"];
        $view->vars["progress_bar_id"] = $progressBarId;
    }
}
