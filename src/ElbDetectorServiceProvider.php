<?php

namespace Sergiqg\AwsElbInstancesDetector;

use \Illuminate\Support\ServiceProvider;

class ElbDetectorServiceProvider extends ServiceProvider
{
    /**
     * Register de Service Provider
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/aws_elb_instance_detector.php', 'aws_elb_instance_detector');
    }

    public function boot()
    {
        $configPath = __DIR__ . '/../config/aws_elb_instance_detector.php';
        $this->publishes([ $configPath => config_path('aws_elb_instance_detector.php') ]);
    }
}