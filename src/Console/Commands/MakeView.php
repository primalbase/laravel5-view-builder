<?php namespace Primalbase\ViewBuilder\Console\Commands;

use Exception;

class MakeView extends CommandBase
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:view {source} {view} {--layout=} {--engine=} {--no-base}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate views uses DreamWeaver Templates.';

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
    $view = $this->argument('view');
    $source = $this->argument('source');
    $layout = $this->option('layout');
    $engine = $this->option('engine');
    $makeBase = !$this->option('no-base');
    $documentRoot = public_path(); // @todo public以外の場合も考慮する

    $viewPath = $this->getViewPath($view, $makeBase);
    if (!$viewPath)
      return;

    $sourcePath = $this->getSourcePath($source, false);
    if (!$sourcePath)
      return;

    $baseView = $this->getBaseView($view);

    try {
      $dwtLayout = $this->getDwtLayout($source, $viewPath, $layout, $makeBase, $baseView, $documentRoot, 'view');
      $dwtLayout->save($engine);
    } catch (Exception $e) {

      $this->error($e->getFile().':'.$e->getLine().' '.$e->getMessage());
      exit(-1);
    }

    $this->info('saved to '.$dwtLayout->saved());
  }
}
