<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

/**
 * Dispatched the moment a class exists by name, before its members are defined —
 * so a definition that references itself (Kubernetes 1.37's CompositePodGroupTemplate
 * lists CompositePodGroupTemplates) resolves to the class being generated instead of
 * generating it again, without end.
 */
class ClassInProgressEvent extends AbstractPHPClassEvent
{
}
