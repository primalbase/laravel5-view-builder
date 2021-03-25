<?php namespace Primalbase\ViewBuilder\Console\Commands;

class UpdateView extends CommandBase
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'update:view';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update views.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $documentRoot = array_get($this->getConfig(), 'documentRoot', public_path());
    $views = array_get($this->getConfig(), 'views', []);
    foreach ($views as $source => $options)
    {
      $view   = array_get($options, 'view', $options);
      $layout = array_get($options, 'layout');
      $engine = array_get($options, 'engine');
      $noBase = array_get($options, 'no-base', false);

      $this->call('make:view', [
        'source' => $source,
        'view' => $view,
        '--layout' => $layout,
        '--engine' => $engine,
        '--no-base' => $noBase,
        '--document-root' => $documentRoot,
      ]);
    }
  }
}
