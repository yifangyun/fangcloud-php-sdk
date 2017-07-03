<?php

$dir = __DIR__ . '/../src';

return new Sami\Sami($dir, array(
    'theme'                => 'default',
    'title'                => 'Fangcloud PHP SDK API',
    'build_dir'            => __DIR__.'/artifacts/docs',
    'cache_dir'            => __DIR__.'/artifacts/docs-cache',
    'default_opened_level' => 1,
));
