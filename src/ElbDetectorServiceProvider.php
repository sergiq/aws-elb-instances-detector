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
        // comment
        $this->mergeConfigFrom(__DIR__ . '/config/aws_elb_instance_detector.php', 'aws_elb_instance_detector');
    }
}