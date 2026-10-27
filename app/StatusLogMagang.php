<?php

namespace App;

enum StatusLogMagang: string
{
    case Pending = 'pending';
    case Revisi = 'revisi';
    case Selesai = 'selesai';
}
