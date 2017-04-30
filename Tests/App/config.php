<?php

$frameworkConfig = array(
    'router' => array(
        'resource' => '%kernel.root_dir%/routing.xml',
    ),
    'secret' => uniqid('', true),
    'test' => true,
);

if (!class_exists('Doctrine\Common\Annotations\Annotation')) {
    $frameworkConfig['annotations'] = false;
}

$container->loadFromExtension('framework', $frameworkConfig);

$container->loadFromExtension('xabbuh_panda', array(
    'clouds' => array(
        'default' => array(
            'id' => 'e122090f4e506ae9ee266c3eb78a8b67',
        ),
    ),
    'accounts' => array(
        'default' => array(
            'access_key' => '799572f795a5a09a251cf2cf46c419ab',
            'secret_key' => 'ae376a8e7bf8faa829dd817f711ee891',
        ),
    ),
));
