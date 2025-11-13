<?php

namespace App\Models;

use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Pipeline\Pipeline;
use App\QueryFilter\Anketa\AnketaName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\AnalysisCandidateResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anketa extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $fillable = [
        'chat_id',
        'json_text',
        'first_name',
        'last_name',
        'father_name',
        'gender',
        'age',
        'martial_status',
        'education',
        'education_place',
        'specialty',
        'state_id',
        'region_id',
        'address',
        'branch_id',
        'position',
        'phone_number',
        'eddition_phone_number',
        'image',
        'payload',
        'citizen',
        'job_now',
        'about_vacancy',
        'height',
        'now_study',
        'type_education',
        'other_info',
        'is_car',
        'about_car',
        'trip',
        'relation_company',
        'worked_company',
        'whether_convicted',
        'whether_convicted_description',
        'salary_last_job',
        'pasport_type',
        'pasport_image_first',
        'pasport_image_second',
        'pnfl',
        'status',
        'test_status',
        'anketa_cancel_manager',
        'replay_status',
        'replay_status_description'
    ];

    protected $baseImageUrl = 'https://garant-hr.uz/api/public/storage/';

    public function getImageAttribute($value)
    {
        return $this->makeFullUrl($value);
    }

    public function getPasportImageFirstAttribute($value)
    {
        return $this->makeFullUrl($value);
    }

    public function getPasportImageSecondAttribute($value)
    {
        return $this->makeFullUrl($value);
    }

    // URL to‘liq bo‘lmasa, to‘liq qiladi
    protected function makeFullUrl($value)
    {
        if (empty($value)) return null;

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return $this->baseImageUrl . ltrim($value, '/');
    }

    public static function anketaFilterSearch($request)
    {
        if ($request->replay_status == 2) {
            $anketa = Anketa::query()
                // ->with(
                //     'interviewCandidat',
                //     'questionResoult',
                //     'anketaCancelByManager',
                //     'anketaChildren',
                //     'anketaProg',
                //     'anketaWorkedBefore',
                //     'anketaLanguage',
                //     'anketaHealthy',
                //     'anketaRelation',
                //     'state',
                //     'region',
                //     'branch',
                // )
                ->filter($request->all());
        } else {
            $anketa = Anketa::query()
                // ->with(
                //     'interviewCandidat',
                //     'questionResoult',
                //     'anketaCancelByManager',
                //     'anketaChildren',
                //     'anketaProg',
                //     'anketaWorkedBefore',
                //     'anketaLanguage',
                //     'anketaHealthy',
                //     'anketaRelation',
                //     'state',
                //     'region',
                //     'branch'
                // )
                ->filter($request->all())
                ->whereNot('status', 0);
        }

        if ($request->was_interviewed == 1 || $request->interview_result != null) {
            $anketa->orderBy(
                InterviewCandidate::select('updated_at')
                    ->whereColumn('interview_candidates.anketa_id', 'anketas.id')
                    ->take(1),
                'desc'
            );

            if ($request->date != null && $request->date[0] && $request->date[1]) {
                $from = Carbon::parse($request->date[0])->format('Y-m-d');
                $to = Carbon::parse($request->date[1])->format('Y-m-d');

                $anketa->whereHas('interviewCandidat', function ($q) use ($from, $to) {
                    $q->whereDate('updated_at', '>=', $from)->whereDate('updated_at', '<=', $to);
                });
            }
        } else {
            if ($request->interview_offer == 1) {
                $anketa->orderBy(
                    InterviewCandidate::select('interview_date')
                        ->whereColumn('interview_candidates.anketa_id', 'anketas.id')
                        ->take(1),
                    'desc'
                );

                if ($request->date != null && $request->date[0] && $request->date[1]) {
                    $from = Carbon::parse($request->date[0])->format('Y-m-d');
                    $to = Carbon::parse($request->date[1])->format('Y-m-d');

                    $anketa->whereHas('interviewCandidat', function ($q) use ($from, $to) {
                        $q->whereDate('interview_date', '>=', $from)->whereDate('interview_date', '<=', $to);
                    });
                }
            } else {
                $anketa->orderBy(
                    AnketaHealthy::select('created_at')
                        ->whereColumn('anketa_healthies.anketa_id', 'anketas.id')
                        ->take(1),
                    'desc'
                );

                if ($request->date != null && $request->date[0] && $request->date[1]) {
                    $from = Carbon::parse($request->date[0])->format('Y-m-d');
                    $to = Carbon::parse($request->date[1])->format('Y-m-d');

                    $anketa->whereHas('anketaHealthy', function ($q) use ($from, $to) {
                        $q->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);
                    });
                }
            }
        }

        $anketa = app(Pipeline::class)
            ->send(
                $anketa
            )
            ->through([
                AnketaName::class,
            ])
            ->thenReturn()
            ->paginate(20);

        return AnalysisCandidateResource::collection($anketa);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function interviewCandidat()
    {
        return $this->hasMany(InterviewCandidate::class);
    }

    public function questionResoult()
    {
        return $this->hasMany(QuestionResoult::class);
    }

    public function anketaChildren()
    {
        return $this->hasMany(AnketaChildren::class);
    }

    public function anketaProg()
    {
        return $this->hasMany(AnketaProg::class);
    }

    public function anketaWorkedBefore()
    {
        return $this->hasMany(AnketaWorkedBefore::class);
    }

    public function anketaLanguage()
    {
        return $this->hasMany(AnketaLanguage::class);
    }

    public function anketaHealthy()
    {
        return $this->hasMany(AnketaHealthy::class);
    }

    public function anketaRelation()
    {
        return $this->hasMany(AnketaRelation::class);
    }

    public function worker()
    {
        return $this->hasMany(Worker::class);
    }

    public function anketaCancelByManager()
    {
        return $this->hasMany(AnketaCancelByManager::class);
    }
}
