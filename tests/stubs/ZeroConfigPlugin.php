<?php

namespace PHPCI;

use PHPCI\Model\Build;

interface ZeroConfigPlugin
{
    public static function canExecute($stage, Builder $builder, Build $build);
}
