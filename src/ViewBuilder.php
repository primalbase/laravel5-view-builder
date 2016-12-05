<?php namespace Primalbase\ViewBuilder;

use Exception;
use Generator;
use Schema;

class ViewBuilder
{
  /** @var Module */
  protected $module;
  protected $instance;
  protected $force;
  protected $targets = [];

  protected function routesPath()
  {
    return app_path().'/Http/routes';
  }

  protected function modelPath()
  {
    return app_path();
  }

  protected function presenterPath()
  {
    return app_path().'/Http/Presenters';
  }

  protected function viewsPath()
  {
    return base_path().'/resources/views';
  }

  protected function requestsPath()
  {
    return app_path().'/Http/Requests';
  }

  public function __construct($module, $force = false, $only = null, $table = null)
  {
    $this->module = new Module($module, $table);
    $this->force = $force;
    $this->targets = ['routes', 'model', 'presenter', 'views', 'requests'];
    if (!is_null($only))
    {
      $only = explode(',', $only);
      $this->targets = array_where($this->targets, function ($key, $value) use ($only) {
        return in_array($value, $only);
      });
    }
    if (!Schema::hasTable($this->module->tableize()))
    {
      throw new Exception($this->module->tableize().'テーブルを作成してください');
    }
  }

  protected function getRoutesFactory()
  {
    $path = $this->routesPath().'/'.$this->module->camel().'.php';
    $factory = new Factories\RoutesFactory($this->module, $path, $this->force);

    return $factory;
  }

  protected function getModelFactory()
  {
    $path = $this->modelPath().'/'.$this->module->studly().'.php';
    $factory = new Factories\ModelFactory($this->module, $path, $this->force);

    return $factory;
  }

  protected function getPresenterFactory()
  {
    $path = $this->presenterPath().'/'.$this->module->studly('Presenter').'.php';
    $factory = new Factories\PresenterFactory($this->module, $path, $this->force);

    return $factory;
  }

  protected function getViewsFactory()
  {
    $path = $this->viewsPath().'/'.$this->module->snake();
    $factory = new Factories\ViewsFactory($this->module, $path, $this->force);

    return $factory;
  }

  protected function getRequestsFactory()
  {
    $path = $this->requestsPath();
    $factory = new Factories\RequestsFactory($this->module, $path, $this->force);

    return $factory;
  }

  /**
   * @return Generator
   */
  public function generator()
  {
    if (in_array('routes', $this->targets))
    {
      $factory = $this->getRoutesFactory();
      $routesPath = $factory->make();
      yield $routesPath.' generated.';
    }

    if (in_array('model', $this->targets))
    {
      $factory = $this->getModelFactory();
      $presenterPath = $factory->make();
      yield $presenterPath.' generated.';
    }

    if (in_array('presenter', $this->targets))
    {
      $factory = $this->getPresenterFactory();
      $presenterPath = $factory->make();
      yield $presenterPath.' generated.';
    }

    if (in_array('views', $this->targets))
    {
      $factory = $this->getViewsFactory();
      foreach ($factory->generator() as $generator)
      {
        yield $generator.' generated.';
      }
    }

    if (in_array('requests', $this->targets))
    {
      $factory = $this->getRequestsFactory();
      foreach ($factory->generator() as $generator)
      {
        yield $generator.' generated.';
      }
    }
  }
}