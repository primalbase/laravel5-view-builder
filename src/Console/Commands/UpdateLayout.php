<?php namespace Primalbase\ViewBuilder\Console\Commands;

class UpdateLayout extends CommandBase
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'update:layout';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update layouts.';

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
    $layouts = array_get($this->getConfig(), 'layouts', []);
    foreach ($layouts as $source => $options)
    {
      $layout = array_get($options, 'layout', $options);
      $engine = array_get($options, 'engine');
      $noBase = array_get($options, 'no-base', false);

      $this->call('make:layout', [
        '--source' => $source,
        '--layout' => $layout,
        '--engine' => $engine,
        '--no-base' => $noBase,
      ]);
    }
  }
}
