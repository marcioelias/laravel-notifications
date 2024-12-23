<?php

namespace MarcioElias\LaravelNotifications\Enums;

enum NotificationType: int
{
    case SMS    = 1;
    case PUSH   = 2;
}
