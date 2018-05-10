<?php

namespace Sergiqg\AwsElbInstancesDetector\Models;

use Aws\Ec2\Ec2Client;
use Aws\ElasticLoadBalancing\ElasticLoadBalancingClient;

class ElbDetector
{
    /**
     * AWS acess config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Elb information.
     *
     * @var \Aws\Result
     */
    protected $elb_information;

    /**
     * List of \Aws\Result.
     *
     * @var array
     */
    protected $ec2_list = [];

    /**
     * Detects all instances attached to a specific ELB.
     *
     * @param array $configuration
     */
    public function detectInstances(string $elb_name = null, array $configuration = []): void
    {
        $this->setConfiguration($configuration);
        $this->processElbInformation($elb_name);
        $this->processInstances();
    }

    /**
     * Return a list of public Ips attached to the Elb.
     *
     * @return array
     */
    public function getInstanceIps(): array
    {
        $ip_list = [];

        foreach ($this->ec2_list as $ec2) {
            $ip_list[] = $ec2->get('Reservations')[ 0 ][ 'Instances' ][ 0 ][ 'PublicIpAddress' ];
        }

        return $ip_list;
    }

    /**
     * Sets the configuration for ELB access.
     *
     * @param array $configuration
     */
    protected function setConfiguration(array $configuration = [])
    {
        $this->config = array_merge(
            [
                'version'     => config('aws_elb_instance_detector.version'),
                'region'      => config('aws_elb_instance_detector.region'),
                'credentials' => [
                    'key'    => config('aws_elb_instance_detector.credentials.key'),
                    'secret' => config('aws_elb_instance_detector.credentials.secret'),
                ],
            ],
            $configuration
        );
    }

    /**
     * Gather all ELB information
     *
     * @param string|null $elb_name
     */
    protected function processElbInformation(string $elb_name = null)
    {
        $elb                   = new ElasticLoadBalancingClient($this->config);
        $this->elb_information = $elb->describeInstanceHealth(
            [
                'LoadBalancerName' => $elb_name ?? config('aws_elb_instance_detector.name'),
            ]
        );
    }

    /**
     * Gather the information of each instance attached to the ELB.
     */
    protected function processInstances()
    {
        $instance_list = $this->elb_information->get('InstanceStates');
        $ec2           = new Ec2Client($this->config);
        foreach ($instance_list as $instance) {
            $this->ec2_list[] = $ec2->describeInstances(
                [
                    'Filters' => [
                        [
                            'Name'   => 'instance-id',
                            'Values' => [ $instance[ 'InstanceId' ] ],
                        ],
                    ],
                ]
            );
        }
    }
}