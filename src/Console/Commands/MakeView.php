<?php namespace Primalbase\ViewBuilder\Console\Commands;

use Exception;

class MakeView extends CommandBase
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:view {source} {view} {--layout=} {--engine=} {--no-base} {--document-root=}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate view uses DreamWeaver Template.';

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
    $documentRoot = $this->option('document-root', public_path());

    $viewPath = $this->getViewPath($view, $makeBase);
    if (!$viewPath)
      return;

    $sourcePath = $this->getSourcePath($source, false, $documentRoot);
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
