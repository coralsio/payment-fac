<?php

namespace Corals\Modules\Payment\Fac\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected function providerBooted()
    {
        $supported_gateways = \Payments::getAvailableGateways();

        $supported_gateways['Fac'] = 'First Atlantic Commerce';

        \Payments::setAvailableGateways($supported_gateways);
    }
}
