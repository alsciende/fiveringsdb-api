<?php

namespace Alsciende\CerealBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlsciendeCerealBundle extends Bundle
{
    const SINGLE_FILE = 1;
    const MULTIPLE_FILES = 2;
    const SINGLE_DATA = 4;
    const MULTIPLE_DATA = 8;
}
