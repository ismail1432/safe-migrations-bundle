<?php

namespace Eniams\SafeMigrationsBundle\Data;

enum DebugCommandHeader: string
{
    case Fqcn = 'Class';
    case Statement = 'Statement';
    case Message = 'Message';
}
