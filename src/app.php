<?php
$app = new Silex\Application();
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
  ));
$app['debug'] = true;
$app['asset_path'] = '/blood-plus/public';

$app->get("/", function () use ($app) {
    $blood = new BloodPlus('','','');
    return $blood->donations();
});

return $app;

