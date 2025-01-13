<?php

declare(strict_types=1);

namespace DaDaDev\Symfony;

final class PhpUrlGeneratingRoutesMapFactory implements UrlGeneratingRoutesMapFactory
{
    /** @var string|null */
    private $urlGeneratingRoutesFile;

    public function __construct(Configuration $configuration)
    {
        $this->urlGeneratingRoutesFile = $configuration->getUrlGeneratingRoutesFile();
    }

    public function create(): UrlGeneratingRoutesMap
    {
        if ($this->urlGeneratingRoutesFile === null) {
            return new FakeUrlGeneratingRoutesMap();
        }

        if (file_exists($this->urlGeneratingRoutesFile) === false) {
            throw new UrlGeneratingRoutesFileNotExistsException(\sprintf('File %s containing route generator information does not exist.', $this->urlGeneratingRoutesFile));
        }

        /** @var array<string, array<string, string>[]> $urlGeneratingRoutes */
        $urlGeneratingRoutes = require $this->urlGeneratingRoutesFile;

        // @phpstan-ignore function.alreadyNarrowedType
        if (!is_array($urlGeneratingRoutes)) {
            throw new UrlGeneratingRoutesFileNotExistsException(\sprintf('File %s containing route generator information cannot be parsed.', $this->urlGeneratingRoutesFile));
        }

        $routes = [];
        foreach ($urlGeneratingRoutes as $routeName => $routeConfiguration) {
            $routes[] = new UrlGeneratingRoute(
                $routeName,
                $routeConfiguration[1]['_controller'],
                $routeConfiguration[2] ?? []
            );
        }

        return new DefaultUrlGeneratingRoutesMap($routes);
    }
}
