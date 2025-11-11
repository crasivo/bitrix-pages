<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Model;

use Crasivo\Pages\Domain\Api\PageContentType;
use Crasivo\Pages\Integration\Validation\Rule\ComponentName;
use Crasivo\Pages\Integration\Validation\Rule\ComponentTemplate;
use Crasivo\Pages\Integration\Validation\Rule\ScalarKeyValue;

class PageComponentModel extends PageRouteModel implements \Crasivo\Pages\Domain\Api\PageComponent
{
    #[ComponentName]
    protected string $componentName;

    #[ScalarKeyValue]
    protected array $componentParams = [];

    #[ComponentTemplate]
    protected string $componentTemplate = '.default';

    /**
     * @inheritDoc
     */
    final public function getContentType(): PageContentType
    {
        return PageContentType::Component;
    }

    /**
     * @inheritDoc
     */
    public function getComponentName(): string
    {
        return $this->componentName;
    }

    /**
     * @inheritDoc
     */
    public function getComponentParams(): array
    {
        return $this->componentParams;
    }

    /**
     * @inheritDoc
     */
    public function getComponentTemplate(): string
    {
        return $this->componentTemplate;
    }

    /**
     * @param string $componentName
     * @return void
     */
    public function setComponentName(string $componentName): void
    {
        $this->componentName = $componentName;
    }

    /**
     * @param array $componentParams
     * @return void
     */
    public function setComponentParams(array $componentParams): void
    {
        $this->componentParams = $componentParams;
    }

    /**
     * @param string|null $componentTemplate
     * @return void
     */
    public function setComponentTemplate(?string $componentTemplate): void
    {
        $this->componentTemplate = $componentTemplate ?? '.default';
    }
}
