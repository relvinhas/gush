<?php

namespace Gush\Helper;

use Symfony\Component\Console\Helper\Helper;
use Gush\Twig\FilterExtension;

class ReportHelper extends Helper
{
    protected $twig;

    public function getName()
    {
        return 'report';
    }

    public function __construct()
    {
        $paths = array(
            __DIR__ . '/../Report/template',
            __DIR__ . '/../Report/template/report',
        );

        $loader = new \Twig_Loader_Filesystem($paths);
        $this->twig = new \Twig_Environment($loader, [
            'autoescape' => false,
        ]);
        $this->twig->addExtension(new FilterExtension());
    }

    public function render($reportName, $data)
    {
        $reportName = $reportName . '.twig';
        return $this->twig->render($reportName, array('data' => $data));
    }
}
