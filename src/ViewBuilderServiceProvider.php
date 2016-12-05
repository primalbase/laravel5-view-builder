<?php namespace Primalbase\ViewBuilder;

use Illuminate\Support\ServiceProvider;

class ViewBuilderServiceProvider extends ServiceProvider
{
  protected $defer = false;

  protected $commands = [
    \Primalbase\ViewBuilder\Console\Commands\MakeLayout::class,
    \Primalbase\ViewBuilder\Console\Commands\MakeView::class,
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
