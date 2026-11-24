<?php

declare(strict_types=1);

use Akeneo\Test\IntegrationTestsBundle\AkeneoIntegrationTestsBundle;
use AkeneoEnterprise\Test\IntegrationTestsBundle\AkeneoEnterpriseIntegrationTestsBundle;
use Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle;
use Acme\Bundle\CustomBundle\AcmeCustomBundle;

return [
    AkeneoIntegrationTestsBundle::class => ['test' => true],
    AkeneoEnterpriseIntegrationTestsBundle::class => ['test' => true],
    PimCustomEntityBundle::class => ['test' => true],
    AcmeCustomBundle::class => ['test' => true],
];
