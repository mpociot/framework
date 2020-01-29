<?php

namespace Illuminate\View;

use Illuminate\Support\Facades\View;
use Illuminate\Filesystem\Filesystem;

class ComponentViewCreator
{
    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Get the path for the component views.
     *
     * @var string
     */
    protected $viewPath;

    public function __construct(Filesystem $files, $viewPath)
    {
        $this->files = $files;
        $this->viewPath = $viewPath;
    }

    /**
     * Return the view file to use when rendering the component.
     *
     * @param Component $component
     * @return string
     */
    public function getView(Component $component)
    {
        if (View::exists($component->view())) {
            return $component->view();
        }

        View::addNamespace('__components', $this->viewPath);

        return $this->createViewFromString($component->view());
    }

    /**
     * Get the path to the compiled version of a view.
     *
     * @param  string  $viewContent
     * @return string
     */
    public function getCompiledPath($viewContent)
    {
        return $this->viewPath.'/'.sha1($viewContent).'.blade.php';
    }

    /**
     * Create a blade view from a given string.
     *
     * @param string $viewContent
     * @return string
     */
    protected function createViewFromString($viewContent)
    {
        $viewFile = $this->getCompiledPath($viewContent);

        if (! $this->files->exists($viewFile)) {
            $this->files->put(
                $viewFile, $viewContent
            );
        }

        return '__components::'.basename($viewFile, '.blade.php');
    }
}
