<?php


namespace App\Models\Interfaces;


interface CustomEvents
{
    /**
     * Return custom event name if it`s happened
     *
     * @param $event_name
     * @return string|null
     */
    public function getCustomEventName($event_name);
}
