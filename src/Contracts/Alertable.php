<?php

namespace MarcioElias\LaravelNotifications\Contracts;

interface Alertable
{
    public function getDestination(): string|null;
}
