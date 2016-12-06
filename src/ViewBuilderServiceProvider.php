<?php namespace Primalbase\ViewBuilder;

use Illuminate\Support\ServiceProvider;
use Primalbase\ViewBuilder\Console\Commands\MakeLayout;
use Primalbase\ViewBuilder\Console\Commands\MakeView;
use Primalbase\ViewBuilder\Console\Commands\UpdateLayout;
use Primalbase\ViewBuilder\Console\Commands\UpdateView;

class ViewBuilderServiceProvider extends ServiceProvider
{
  protected $defer = false;

  protected $commands = [
    MakeLayout::class,
    MakeView::class,
    UpdateLayout::class,
    UpdateView::class,
  ];

  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot()
  {
    $this->publishes([
      __DIR__.'/../config/viewbuilder.php' => config_path('viewbuilder.php'),
    ]);
  }

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    $this->commands($this->commands);
  }

  public function provides()
  {
  }
}
