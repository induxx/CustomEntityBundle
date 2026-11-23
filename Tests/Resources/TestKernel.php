<?php

class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        $registered = [];
        foreach (parent::registerBundles() as $bundle) {
            $registered[$bundle::class] = true;
            yield $bundle;
        }

        foreach (require __DIR__.'/bundles.php' as $class => $environments) {
            if (!isset($registered[$class]) && ($environments[$this->environment] ?? $environments['all'] ?? false)) {
                yield new $class();
            }
        }
    }
}
