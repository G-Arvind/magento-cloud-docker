<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CloudDocker\Config;

use Magento\CloudDocker\App\ConfigurationMismatchException;
use Magento\CloudDocker\Service\ServiceInterface;

/**
 * Generates relationship data for current configuration
 * based on services in .magento/service.yaml and relationships in .magento.app.yaml
 */
class Relationship
{
    /**
     * Service names
     */
    private const RELATIONSHIP_DATABASE = 'database';
    private const RELATIONSHIP_DATABASE_QUOTE = 'database-quote';
    private const RELATIONSHIP_DATABASE_SALES = 'database-sales';
    private const REDIS = 'redis';
    private const ELASTICSEARCH = 'elasticsearch';
    private const RABBITMQ = 'rabbitmq';

    /**
     * Default relationships configuration
     *
     * @var array
     */
    private static $defaultConfiguration= [
        self::RELATIONSHIP_DATABASE => [
            [
                'host' => 'db',
                'path' => 'magento2',
                'password' => 'magento2',
                'username' => 'magento2',
                'port' => '3306'
            ],
        ],
        self::RELATIONSHIP_DATABASE_SALES => [
            [
                'host' => 'db-sales',
                'path' => 'magento2',
                'password' => 'magento2',
                'username' => 'magento2',
                'port' => '3306'
            ],
        ],
        self::RELATIONSHIP_DATABASE_QUOTE => [
            [
                'host' => 'db-quote',
                'path' => 'magento2',
                'password' => 'magento2',
                'username' => 'magento2',
                'port' => '3306'
            ],
        ],
        self::REDIS => [
            [
                'host' => 'redis',
                'port' => '6379'
            ]
        ],
        self::ELASTICSEARCH => [
            [
                'host' => 'elasticsearch',
                'port' => '9200',
            ],
        ],
        self::RABBITMQ => [
            [
                'host' => 'rabbitmq',
                'port' => '5672',
                'username' => 'guest',
                'password' => 'guest',
            ]
        ],
    ];

    /**
     * Generates relationship data for current configuration
     *
     * @param Config $config
     * @return array
     * @throws ConfigurationMismatchException
     */
    public function get(Config $config): array
    {
        $relationships = [];
        foreach (self::$defaultConfiguration as $serviceName => $serviceConfig) {
            if ($config->hasServiceEnabled($this->convertServiceName($serviceName))) {
                $relationships[$serviceName] = $serviceConfig;
            }
        }

        return $relationships;
    }

    /**
     * Convert services names for compatibility with `getServiceVersion` method.
     *
     * @param string $serviceName
     * @return string
     */
    private function convertServiceName(string $serviceName): string
    {
        $map = [
            self::RELATIONSHIP_DATABASE => ServiceInterface::SERVICE_DB,
            self::RELATIONSHIP_DATABASE_QUOTE => ServiceInterface::SERVICE_DB_QUOTE,
            self::RELATIONSHIP_DATABASE_SALES => ServiceInterface::SERVICE_DB_SALES,
        ];

        return $map[$serviceName] ?? $serviceName;
    }
}
