<?php

namespace Corals\Modules\Payment\Fac\Providers;

use Corals\Foundation\Providers\BaseUpdateModuleServiceProvider;

class UpdateModuleServiceProvider extends BaseUpdateModuleServiceProvider
{
    protected $module_code = 'corals-payment-fac';
    protected $batches_path = __DIR__ . '/../update-batches/*.php';
}
