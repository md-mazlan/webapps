<?php

/**
 * DunSeat Class
 * Handles all database interactions for a single Sabah DUN seat.
 */
class DunSeat
{
    // Public properties to hold the seat data.
    public $code;
    public $seat;
    /**
     * Constructor to initialize the object with a database connection.
     * @param mysqli $conn The database connection object.
     * @param string|null $code The DUN seat code (e.g., 'N01').
     * @param string|null $seat The DUN seat name (e.g., 'Banggi').
     */
    public function __construct($code = null, $seat = null)
    {
        $this->code = $code;
        $this->seat = $seat;
    }
}