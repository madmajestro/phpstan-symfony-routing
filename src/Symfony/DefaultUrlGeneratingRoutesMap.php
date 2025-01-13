<?php

declare(strict_types=1);

namespace DaDaDev\Symfony;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;

final class DefaultUrlGeneratingRoutesMap implements UrlGeneratingRoutesMap
{
    /** @var UrlGeneratingRoutesDefinition[] */
    private $routes;

    /**
     * @param UrlGeneratingRoutesDefinition[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function hasRouteName(string $name): bool
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function getRouteRequirements(string $name): array
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route->getRequiredUrlParams();
            }
        }

        return [];
    }

    public static function getRouteNameFromNode(Expr $node, Scope $scope): ?string
    {
        $strings = $scope->getType($node)->getConstantStrings();

        return \count($strings) === 1 ? $strings[0]->getValue() : null;
    }
}
