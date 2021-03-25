<?php namespace Primalbase\ViewBuilder\Console\Commands;

use Exception;

class MakeLayout extends CommandBase
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'make:layout {--source=main.dwt} {--layout=layout.main} {--engine=blade} {--no-base} {--document-root=}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate layout uses DreamWeaver Template.';

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
    $source = $this->option('source');
    $layout = $this->option('layout');
    $engine = $this->option('engine');
    $makeBase = !$this->option('no-base');
    $documentRoot = $this->option('document-root', public_path());

    $viewPath = $this->getViewPath($layout, $makeBase);
    if (!$viewPath)
      return;

    $sourcePath = $this->getSourcePath($source, true, $documentRoot);
    if (!$sourcePath)
      return;

    $baseView = $this->getBaseView($layout);


    try {
      $dwtLayout = $this->getDwtLayout($sourcePath, $viewPath, $layout, $makeBase, $baseView, $documentRoot, 'layout');
      $dwtLayout->save($engine);
    } catch (Exception $e) {

      $this->error($e->getFile().':'.$e->getLine().' '.$e->getMessage());
      exit(-1);
    }

    $this->info('saved to '.$dwtLayout->saved());
  }
}
