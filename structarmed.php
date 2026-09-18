<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;

return Architecture::define()
    ->withPresets(Preset::PER(), Preset::CODEQUALITY())
    ->layer('Pipe', [
        'src/EmptyPipelineHandler.php',
        'src/IterableMiddlewarePipeInterface.php',
        'src/MiddlewarePipe.php',
        'src/MiddlewarePipeInterface.php',
        'src/Next.php',
    ])
    ->layer('Utils', 'src/Utils.php')
    ->layer('Exception', 'src/Exception')
    ->layer('Handler', 'src/Handler')
    ->layer('Middleware', 'src/Middleware')
    ->ruleset([
        'Utils'      => [],
        'Handler'    => [],
        'Exception'  => ['Middleware'],
        'Middleware' => ['+Exception', 'Utils'],
        'Pipe'       => ['Exception'],
    ]);
