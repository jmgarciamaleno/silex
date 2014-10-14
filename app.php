<?php

use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

use jmgm\simple\Document\HelloWorld;
use jmgm\simple\Document\Dot;

const POINT_CLASS = 'jmgm\simple\Document\Dot';

if (!\file_exists($file = __DIR__.'/vendor/autoload.php')) {
    throw new \RuntimeException('Install dependencies to run this script.');
}

$loader = require_once $file;

/* Register documents */
$loader->add('/src/jmgm/simple/Document', __DIR__);

/* Silex setup */
$app = new Silex\Application();
$app['debug'] = true;

/* Routing */
// hi
$app->get('/hi/{name}', function ($name) {
    $saluter = new HelloWorld();
    $salute = $saluter->hi($name);

    return "$salute";
});

// setDot
$app->get('/setDot/{x}/{y}', function ($x, $y) {
    $dm = doctrineODMSetup();
    $dot = new Dot((int) $x, (int) $y);

    try {
        $dm->persist($dot);
        $dm->flush();
    } catch (\Exception $e) {
        throw new \Exception("Error saving object dot.");
    }

    return new JsonResponse("Object saved.", 201);
});

// getDot
$app->get('/getDot/{id}', function ($id) {
    $dm = doctrineODMSetup();

    try {
        $dot = $dm->find(POINT_CLASS, $id);
    } catch (\Exception $e) {
        throw new \Exception("Error retrieving object with id: $id .");
    }

    if (!$dot) {
        return new JsonResponse("Object not found.", 400);
    }

    return new JsonResponse($dot->toArray(), 200);
});

/* Silex init */
$app->run();

/**
 * Doctrine ODM config and setup
 *
 * @return $dm DocumentManager
 **/
function doctrineODMSetup()
{
    // Doctrine MongoDB ODM setup
    $connection = new Connection();

    $config = new Configuration();
    $config->setProxyDir(__DIR__ . '/Proxies');
    $config->setProxyNamespace('Proxies');
    $config->setHydratorDir(__DIR__ . '/Hydrators');
    $config->setHydratorNamespace('Hydrators');
    $config->setDefaultDB('silex');
    $config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__ . '/src/jmgm/simple/Document'));

    AnnotationDriver::registerAnnotationClasses();

    $dm = DocumentManager::create($connection, $config);

    return $dm;
}

