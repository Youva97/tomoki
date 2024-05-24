<?php

namespace App\Twig\Runtime;

use App\Entity\Script;
use App\Repository\ScriptRepository;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{


    private $repo;
    public function __construct(ScriptRepository $repo)
    {
        $this->repo = $repo;
    }

    public function doSomething()
    {
        return $this->repo->findOneBy(1)->getScript();
    }
}
