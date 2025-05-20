<?php

namespace Aselsan\DayOff\Models;

use CodeIgniter\Model;

class DayOffModel extends Model
{
    protected $table            = 'indonesian_dayoffs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'date',
        'title',
    ];

    public function now()
    {
        return $this
            ->where('date', date('Y-m-d'))
            ->first();
    }

    public function yesterday()
    {
        return $this
            ->where('date', date('Y-m-d', strtotime('-1 day')))
            ->first();
    }

    public function tomorrow()
    {
        return $this
            ->where('date', date('Y-m-d', strtotime('+1 day')))
            ->first();
    }

    public function thisWeek()
    {
        return $this
            ->where('date >=', date('Y-m-d', strtotime('this week')))
            ->where('date <=', date('Y-m-d', strtotime('this week +6 days')))
            ->findAll();
    }

    public function thisMonth()
    {
        return $this
            ->where('date >=', date('Y-m-01'))
            ->where('date <=', date('Y-m-t'))
            ->findAll();
    }

    public function thisYear()
    {
        return $this
            ->where('date >=', date('Y-01-01'))
            ->where('date <=', date('Y-12-31'))
            ->findAll();
    }
}
