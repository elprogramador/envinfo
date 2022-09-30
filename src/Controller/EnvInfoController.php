<?php

namespace Jlle\EnvInfo\Controller;

use Jlle\EnvInfo\Entity\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Jlle\EnvInfo\Inicio\Inicial;

class EnvInfoController extends AbstractController
{
    public function envInfo(KernelInterface $kernelInterface): Response
    {
        $allowUse = false;
        $util = new Util();
        $variables = array();
        $finder = new Finder();
        $finder->files()->ignoreDotFiles(false)->in(dirname(__DIR__, 5));
        if ($finder->hasResults()) {}
        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();
            if (str_contains($fileNameWithExtension, "envinfopass")) {
                $allowUse = true;
            }
            $environment = $kernelInterface->getEnvironment();
            if ($environment == "dev") {
                $environment = 'local';
            }
            if (str_contains($fileNameWithExtension, ".env." . $environment) || $util->endsWith($fileNameWithExtension, '.env')) {
                $handle = fopen($file, "r");
                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        if (str_contains($line, "=")) {
                            if (str_contains($line, "#")) {
                                $line = substr($line, 0, strpos($line, "#"));
                            }
                            if ($line != "") {
                                $parts = explode("=", $line, 2);
                                if (!array_key_exists($parts[0], $variables)) {
                                    $variables[$parts[0]] = [
                                      'value' => $parts[1],
                                      'extension' => $fileNameWithExtension,
                                    ];
                                } else {
                                    if ($fileNameWithExtension!= "env") {
                                        $variables[$parts[0]] = [
                                            'value' => $parts[1],
                                            'extension' => $fileNameWithExtension,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                    fclose($handle);
                }
            }
        }
        array_multisort(array_column($variables, 'extension'), SORT_ASC, $variables);
        $str = "";
        foreach ($variables as $key => $var) {
            $parExtension = explode(".", $var['extension']);
            if (isset($parExtension[2])) {
                $strExtension = ".".$parExtension[1].".".$parExtension[2];
            } else {
                $strExtension = ".".$parExtension[1];
            }
            $str .= "<span style='font-weight: bold'>[".$strExtension."]</span> <span style='color: green; font-weight: bold'>".$key."</span> = ".$var['value']."<br>";
        }

        if ($allowUse === false) {
            $str = "Out of use";
        }

        return new Response(
            '<html><body>'.$str.'</body></html>'
        );
    }
}
