<?php

if (! enum_exists('SortDirection', false)) {
    enum SortDirection
    {
        case Ascending;
        case Descending;
    }
}
