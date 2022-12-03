<?php
namespace core\enums;


abstract class ReservationStatus
{
    const  reserved = "1",
        on_progress = "2",
        cancel = "3",
        accomplished = "4",
        no_show = "5",
        pending = "6",
        not_available = "7",
        archive = "8";
}