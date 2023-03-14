<?php

namespace App\Http\Resources;

use App\Http\Resources\JobList\Buyer;
use App\Http\Resources\JobList\Job;
use App\Http\Resources\JobList\Seller;
use Illuminate\Http\Resources\Json\JsonResource;

class JobListMessage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>(string)$this->id,
            'seller_id'=>new Seller($this->seller),
            'buyer_id'=>new Buyer($this->buyer),
            'job_id'=>new Job($this->job),
            'timestamp'=>$this->created_at
        ];
    }
}
