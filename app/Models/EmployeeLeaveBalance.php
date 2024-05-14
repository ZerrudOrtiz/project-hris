<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLeaveBalance extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'leave_balance_id';

    protected $fillable = ['employee_id','type','description', 'balance', 'used_balance', 'remaining_balance', 'is_paid'];
}
