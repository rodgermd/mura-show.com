<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
   'Symfony'          => array(__DIR__.'/../vendor/symfony/src', __DIR__.'/../vendor/bundles'),
   'Sensio'           => __DIR__.'/../vendor/bundles',
   'JMS'              => __DIR__.'/../vendor/bundles',
   'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
   'Doctrine\\DBAL'   => array(__DIR__.'/../vendor/doctrine-dbal/lib', __DIR__.'/../vendor/doctrine-migrations/lib'),
   'Doctrine\\Common\\DataFixtures' => __DIR__.'/../vendor/doctrine-fixtures/lib',
   'Doctrine\\Common' => __DIR__.'/../vendor/doctrine-common/lib',
   'Doctrine'         => __DIR__.'/../vendor/doctrine/lib',
   'Monolog'          => __DIR__.'/../vendor/monolog/src',
   'Assetic'          => __DIR__.'/../vendor/assetic/src',
   'Metadata'         => __DIR__.'/../vendor/metadata/src',
   'Gedmo'            => __DIR__.'/../vendor/bundles/gedmo-doctrine-extensions/lib',
   'Stof'             => __DIR__.'/../vendor/bundles',
   'FOS'              => __DIR__.'/../vendor/bundles',
   'WhiteOctober\PagerfantaBundle' => __DIR__.'/../vendor/bundles',
   'Pagerfanta'                    => __DIR__.'/../vendor/bundles/pagerfanta/src',
    'BCC' => __DIR__.'/../vendor/bundles',

    'Behat\\BehatBundle' => __DIR__.'/../vendor/bundles',
    'Behat\\MinkBundle'  => __DIR__.'/../vendor/bundles',
    'Behat\\Gherkin'      => '/home/rodger/web/test-dependencies/behat/gherkin/src',
    'Behat\\Behat'        => '/home/rodger/web/test-dependencies/behat/src',
    'Goutte'        => '/home/rodger/web/test-dependencies/goutte/src',
    'Zend'        => '/home/rodger/web/test-dependencies/zf2/library',
));
$loader->registerPrefixes(array(
    'Twig_Extensions_' => __DIR__.'/../vendor/twig-extensions/lib',
    'Twig_'            => __DIR__.'/../vendor/twig/lib',
    'PHPParser'        => __DIR__.'/../vendor/php-parser/lib'
));

// intl
  if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->registerPrefixFallbacks(array(__DIR__.'/../vendor/symfony/src/Symfony/Component/Locale/Resources/stubs'));
  }

  $loader->registerNamespaceFallbacks(array(
                                           __DIR__.'/../src',
                                      ));
  $loader->register();

  AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
  });
  AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

  require __DIR__.'/../vendor/swiftmailer/lib/swift_required.php';
