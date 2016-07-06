<?php

namespace nicolascajelli\server\di;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
class ContainerBuilder extends SymfonyContainerBuilder
{

    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $id = str_replace('\\', '.', trim($id, '\\'));
        return parent::get($id, $invalidBehavior);
    }
}