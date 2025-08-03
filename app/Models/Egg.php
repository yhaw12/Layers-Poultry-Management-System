<?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Database\Eloquent\SoftDeletes;
   use Illuminate\Database\Eloquent\Factories\HasFactory;

   class Egg extends Model
   {
       use SoftDeletes;
       use HasFactory;

       protected $fillable = [
           'pen_id',
           'crates',
           'small_eggs',
           'medium_eggs',
           'large_eggs',
           'cracked_eggs',
           'collected_by',
           'date_laid',
           'created_by',
       ];

       protected $casts = [
           'date_laid' => 'date',
           'crates' => 'decimal:2',
       ];

       protected $dates = ['deleted_at'];

       public function createdBy()
       {
           return $this->belongsTo(User::class, 'created_by');
       }

       public function collectedBy()
       {
           return $this->belongsTo(User::class, 'collected_by');
       }

       public function pen()
       {
           return $this->belongsTo(Pen::class, 'pen_id');
       }
   }
   