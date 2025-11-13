<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Model;
use App\QueryFilter\RuleForEmployee\RuleText;
use App\Http\Resources\RuleForEmployeesResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RulesForEmployee extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'rule_text',
        'rules_for_employee_sub_category_id',
        'position_status',
        'common_status',
        'values_status',
        'positions',
        'qoida_image_array_path',
        'code',
        'responsible_worker',
    ];

    protected $casts = [
        'qoida_image_array_path' => 'array'
    ];

    public static function searchFilter($request)
    {
        $data = app(Pipeline::class)
            ->send(
                RulesForEmployee::query()->filter($request->all())
            )
            ->through([
                RuleText::class
            ])
            ->thenReturn()
            ->get();

        if ($request->filter == null) {
            $sq = [];
            $sq1 = [];
        }
        $rulesSequenceNumber = RulesSequenceNumber::query()->where('rule_section', $request->filter)->first();
        if ($rulesSequenceNumber) {
            $sq = collect(json_decode($rulesSequenceNumber->json_sequence_number))->whereIn('rule_id', $data->where('rules_for_employee_sub_category_id', null)->pluck('id'))->flatten();
        } else {
            $sq = [];
            $sq1 = [];
        }

        $rules_sequence_number_list_for_sub_category = [];
        $subCategory = RulesForEmployeeSubCategory::all();
        foreach ($data->whereNotNull('rules_for_employee_sub_category_id')->groupBy('rules_for_employee_sub_category_id') as $key => $val) {

            if ($rulesSequenceNumber) $sq1 = collect(json_decode($rulesSequenceNumber->json_sequence_number))->whereIn('rule_id', $data->where('rules_for_employee_sub_category_id', $key)->pluck('id'))->flatten();

            array_push($rules_sequence_number_list_for_sub_category, [
                'sub_category_id' => $key,
                'sub_category_name' => $subCategory->where('id', $key)->first()->name ?? null,
                'rules' => RuleForEmployeesResource::collection($val),
                'rules_count' => count(RuleForEmployeesResource::collection($val)),
                'rules_sequence_number_list' => $sq1
            ]);
        }

        return [
            'rules' => RuleForEmployeesResource::collection($data->where('rules_for_employee_sub_category_id', null)),
            'rules_count' => count(RuleForEmployeesResource::collection($data)),
            'rules_sequence_number_list' => $sq,
            'sub_category_list' => $rules_sequence_number_list_for_sub_category,
        ];
    }

    public function rulesForEmployeeHistory()
    {
        return $this->hasMany(RulesForEmployeeHistory::class);
    }

    public function rulesForEmployeeSubCategory()
    {
        return $this->belongsTo(RulesForEmployeeSubCategory::class);
    }

}
