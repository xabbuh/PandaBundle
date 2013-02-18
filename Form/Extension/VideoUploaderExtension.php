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
        $resolver->setOptional(array("panda_widget", "panda_widget_version"));
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
            if (isset($options["panda_widget_version"])) {
                $widgetVersion = $options["panda_widget_version"];
            } else {
                $widgetVersion = 2;
            }

            $view->vars["attr"]["panda_uploader"] = "v$widgetVersion";
        }
    }
}
