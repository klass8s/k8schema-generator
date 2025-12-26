<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Type;

use Nette\PhpGenerator\InterfaceType;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Printer\PsrPrinter;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\Property;

final class PHPClass
{
    private array $uses = [];
    private array $lines = [];

    public function __construct(
        private readonly ClassName $name,
        private readonly ClassType|InterfaceType $class,
        public readonly bool $isExternal = false,
    ) {
    }

    public function name(): ClassName
    {
        return $this->name;
    }

    public function classType(): ClassType|InterfaceType
    {
        if ($this->isExternal) {
            throw new \LogicException('ClassType instance is dummy in external PHP classes');
        }

        return $this->class;
    }

    public function toString(Context $context): string
    {
        if ($this->isExternal) {
            throw new \LogicException('External PHP classes should not be generated');
        }

        $this->sortMethods();

        $this
            ->addUsesFromImplements()
            ->addUsesFromProperties($context)
            ->addUsesFromMethods($context);

        $this
            ->line('<?php ')
            ->line('')
            ->line('namespace '.$this->name->namespace().';')
            ->line('')
            ->renderUses();

        $fileCode = \implode(PHP_EOL, $this->lines);
        $classCode = new PsrPrinter()->printClass($this->class);

        return $fileCode.PHP_EOL.$classCode;
    }

    public function useClass(ClassName $className): self
    {
        $this->uses[] = ltrim($className->fqcn(), '\\');

        return $this;
    }

    private function addUsesFromImplements(): self
    {
        $implements = $this->class instanceof ClassType
            ? $this->class->getImplements()
            : $this->class->getExtends();

        $newImplements = [];
        foreach ($implements as $fqcn) {
            $className = ClassName::fromFQCN($fqcn);
            if ($className->shortName() !== $this->name->shortName()) {
                $this->useClass($className);
                $newImplements[] = $className->shortName();
            } else {
                $newImplements[] = $fqcn;
            }
        }

        if ($this->class instanceof ClassType) {
            $this->class->setImplements($newImplements);
        } elseif ($this->class instanceof InterfaceType) {
            $this->class->setExtends($newImplements);
        }

        return $this;
    }

    private function addUsesFromProperties(Context $context): self
    {
        if (!$this->class instanceof ClassType) {
            return $this;
        }

        foreach ($this->class->getProperties() as $property) {
            $type = $property->getType();
            if (null === $type) {
                continue;
            }
            $this->deleteCurrentNamespaceFromComment($property);
            $this->addUsesFromComment($property, $context);
            if (!ClassName::isFQCN($type)) {
                continue;
            }
            $className = ClassName::fromFQCN($type);
            $this->addUse($className);
            $property->setType($className->shortName());
            $this->deleteClassNamespaceComment($className, $property);
        }

        return $this;
    }

    private function addUsesFromMethods(Context $context): void
    {
        foreach ($this->class->getMethods() as $method) {
            $returnType = $method->getReturnType();
            $this->deleteCurrentNamespaceFromComment($method);
            $this->addUsesFromComment($method, $context);
            if (null !== $returnType && ClassName::isFQCN($returnType)) {
                $className = ClassName::fromFQCN($returnType);
                $this->addUse($className);
                $this->deleteClassNamespaceComment($className, $method);
                $method->setReturnType($className->shortName());
            }

            foreach ($method->getParameters() as $parameter) {
                $this->addUsesFromParam($parameter, $method);
            }
            if (null !== $method->getBody()) {
                $body = $this->replaceFQCNs($method->getBody(), $context);
                $method->setBody($body);
            }
        }
    }

    private function addUsesFromParam(Parameter $param, Method $method): void
    {
        $type = $param->getType();
        if (null === $type) {
            return;
        }
        if (!ClassName::isFQCN($type)) {
            return;
        }
        $className = ClassName::fromFQCN($type);
        $this->addUse($className);

        $this->deleteClassNamespaceComment($className, $method);
        $param->setType($className->shortName());
    }

    private function addUsesFromComment(Method|Property $commentAware, Context $context): void
    {
        $comment = $commentAware->getComment();
        if (!$comment) {
            return;
        }

        $comment = $this->replaceFQCNs($comment, $context);
        $commentAware->setComment($comment);
    }

    private function deleteClassNamespaceComment(ClassName $className, Method|Property $commentAware): void
    {
        $comment = $commentAware->getComment();
        if ($comment) {
            $comment = str_replace($className->fqcn(), $className->shortName(), $comment);
            $commentAware->setComment($comment);
        }
    }

    private function deleteCurrentNamespaceFromComment(Method|Property $commentAware): void
    {
        $comment = $commentAware->getComment();
        if ($comment) {
            $comment = str_replace($this->name->namespace().'\\', '', $comment);
            $commentAware->setComment($comment);
        }
    }

    private function addUse(ClassName $className): void
    {
        if ($className->namespace() !== $this->name->namespace()) {
            $this->useClass($className);
        }
    }

    private function renderUses(): void
    {
        $this->uses = array_unique($this->uses);
        sort($this->uses);
        foreach ($this->uses as $fqcn) {
            $this->lines[] = 'use '.$fqcn.';';
        }
        $this->lines[] = '';
    }

    private function line(string $line): self
    {
        $this->lines[] = $line;

        return $this;
    }

    private function replaceFQCNs(string $text, Context $context): string
    {
        $pattern = sprintf(
            '/%s\\\([a-z0-9][\\\]?)+/i',
            preg_quote($context->namespacePrefix),
        );
        preg_match_all($pattern, $text, $matches);

        foreach ($matches[0] as $match) {
            if (!ClassName::isFQCN($match)) {
                continue;
            }
            $className = ClassName::fromFQCN($match);
            $this->addUse($className);
            $text = str_replace($className->fqcn(), $className->shortName(), $text);
        }

        return $text;
    }

    private function sortMethods(): void
    {
        $class = $this->class;
        $sortFunc = function (Method $lhs, Method $rhs) {
            // Constructor always first
            if ('__construct' === $lhs->getName()) {
                return -1;
            } elseif ('__construct' === $rhs->getName()) {
                return 1;
            }

            // jsonSerialize() always last
            if ('jsonSerialize' === $lhs->getName()) {
                return 1;
            } elseif ('jsonSerialize' === $rhs->getName()) {
                return -1;
            }

            // Public methods first and so on
            if ($lhs->getVisibility() !== $rhs->getVisibility()) {
                $order = ['public' => 1, 'protected' => 2, 'private' => 3];

                return $order[$lhs->getVisibility()] - $order[$rhs->getVisibility()];
            }

            $names = [$lhs->getName(), $rhs->getName()];
            \sort($names);

            // Sort names alphabetically
            return \array_search($lhs->getName(), $names) - \array_search($rhs->getName(), $names);
        };

        $methods = $class->getMethods();
        \usort($methods, $sortFunc);
        $class->setMethods($methods);
    }
}
