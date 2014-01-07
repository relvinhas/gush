<?php

/*
 * This file is part of the Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\UseCase\Support;

use Symfony\Component\Console\Input\StringInput;

class InteractiveStringInput extends StringInput
{
    public function setInteractive($interactive)
    {
        // this function is disabled to prevent setting non interactive mode on string input after posix_isatty return false
    }
}