<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    /**
     * Get positions as JSON for dropdown/select
     */
    public function getList()
    {
        $positions = Position::where('deleted_at', null)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $positions]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query with soft deletes
        $query = Position::withTrashed()->with(['user', 'section']);

        // Apply search
        if ($request->filled('search')) {
            $query = $query->search($request->search);
        }

        // Apply sorting
        $sortKey = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query = $query->orderBy($sortKey, $sortDirection);
        }

        // Paginate
        $positions = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Documents/positions', [
            'positions' => $positions,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        try {
            DB::beginTransaction();
            $section = Section::findOrFail($request->section_id);
            $lastPosition = Position::query()->latest('id')->first();
            $lastCode = $lastPosition ? (int)$lastPosition->code : 0;
            $code = str_pad($lastCode + 1, 9, '0', STR_PAD_LEFT);
            $dataPostion = [
                "ticket" => $this->getTicket(),
                "Kod" => $code,
                "Job" => $request->name,
                "section_guid" => $section->guid,
            ];

            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, env('POSITION_1C_API_HOST'));
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($dataPostion));
            curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type:application/json', env('HR')]);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch1, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');

            $result2 = curl_exec($ch1);

            if ($result2 === false) {
                throw new Exception("Position yuborishda xatolik: " . curl_error($ch1));
            }

            $decoded2 = json_decode($result2, true);
            if (empty($decoded2['GUID'])) {
                throw new Exception("Position GUID qaytmadi.");
            }

            $guid = $decoded2['GUID'];

            Position::create([
                'name' => $request->name,
                'does_it_belong_to_the_curator' => $request->does_it_belong_to_the_curator ?? false,
                'guid' => $guid,
                'code' => $code,
                'responsible_worker' => auth()->user()->name,
                'section_id' => $request->section_id,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Lavozim muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lavozim yaratishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        try {
            DB::beginTransaction();
            $ticket = $this->getTicket();

            $section = Section::find($request->section_id);
            $data = [
                "ticket" => $ticket,
                "GUID" => $position->guid,
                "Job" => $request->name,
                "section_guid" => $section->guid,
            ];

            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, env('UPDATE_POSITION_1C_API_HOST'));
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type: application/json', env('HR')]);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch1, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            $result = curl_exec($ch1);

            if ($result === false) {
                throw new Exception("Position update bo‘lmadi: " . curl_error($ch1));
            }

            $result = json_decode($result, true);
            if (empty($result['GUID'])) {
                throw new Exception("Position update bo‘lmadi.");
            }
            $position->update([
                'name' => $request->name,
                'responsible_worker' => auth()->user()->name,
                'section_id' => $request->section_id,
                'does_it_belong_to_the_curator' => $request->does_it_belong_to_the_curator ?? false,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Lavozim muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lavozim yangilashda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        try {
            // Check if position was created today
            if (!$position->created_at->isToday()) {
                throw new \Exception("Lavozimni faqat yaratilgan kuni o'chirish mumkin.");
            }

            // Agar shu lavozimlarda ishchilar mavjud bo‘lsa
            $is_worker_position = Worker::query()->where('position_id', $position->id)->exists();

            if ($is_worker_position) {
                throw new \Exception("Бу лавозимда ишчи бор, ўчириб бўлмайди!");
            }

            $ticket = $this->getTicket();

            // Har bir GUID ni 1C ga yuborish
            $payload = [
                "ticket" => $ticket,
                "GUID" => $position->guid,
            ];

            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, env('DELETE_POSITION_1C_API_HOST'));
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type: application/json', env('1C_API_TOKEN')]);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch1, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');

            $result = curl_exec($ch1);

            if (!$result) {
                throw new \Exception("1C position o‘chirishda xatolik: " . curl_error($ch1));
            }

            $deleteResponse = json_decode($result, true);

            if (isset($deleteResponse['error'])) {
                throw new \Exception("1C xato: " . $deleteResponse['error']);
            }

            $position->delete(); // Soft delete
//            PositionCoefficient::query()->where('position_guid', $position->guid)->delete();

            return redirect()->back()->with('success', 'Lavozim muvaffaqiyatli o\'chirildi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete positions
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday lavozim tanlanmagan!');
            }

            // Get positions and check if all were created today
            $positions = Position::whereIn('id', $ids)->get();
            $notToday = $positions->filter(fn($p) => !$p->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi lavozimlar bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan lavozimlarni o'chirish mumkin.");
            }

            foreach ($positions as $position) {
                // Agar shu lavozimlarda ishchilar mavjud bo‘lsa
                $is_worker_position = Worker::query()->where('position_id', $position->id)->exists();

                if ($is_worker_position) {
                    throw new \Exception("Бу лавозимда ишчи бор, ўчириб бўлмайди!");
                }

                $ticket = $this->getTicket();

                // Har bir GUID ni 1C ga yuborish
                $payload = [
                    "ticket" => $ticket,
                    "GUID" => $position->guid,
                ];

                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, env('DELETE_POSITION_1C_API_HOST'));
                curl_setopt($ch1, CURLOPT_POST, 1);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type: application/json', env('1C_API_TOKEN')]);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch1, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');

                $result = curl_exec($ch1);

                if (!$result) {
                    continue;
                }

                $deleteResponse = json_decode($result, true);

                if (isset($deleteResponse['error'])) {
                    continue;
                }

                $position->delete(); // Soft delete
                PositionCoefficient::query()->where('position_guid', $position->guid)->delete();
            }

            $count = Position::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', "{$count} ta lavozim muvaffaqiyatli o'chirildi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk restore positions
     */
    public function bulkRestore(Request $request)
    {
        try {
            if (true) {
                throw new \Exception("Lavozimni qayta tiklash mavjud emas");
            }

            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday lavozim tanlanmagan!');
            }

            // Get deleted positions and check if all were created today
            $positions = Position::onlyTrashed()->whereIn('id', $ids)->get();
            $notToday = $positions->filter(fn($p) => !$p->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi lavozimlar bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan lavozimlarni tiklash mumkin.");
            }

            $count = Position::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()->back()->with('success', "{$count} ta lavozim muvaffaqiyatli tiklandi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Restore deleted position
     */
    public function restore($id)
    {
        try {
            if (true) {
                throw new \Exception("Lavozimni qayta tiklash mavjud emas");
            }

            $position = Position::withTrashed()->findOrFail($id);

            if (!$position->trashed()) {
                return redirect()->back()->with('error', 'Bu lavozim allaqachon aktiv!');
            }

            // Check if position was created today
            if (!$position->created_at->isToday()) {
                throw new \Exception("Lavozimni faqat yaratilgan kuni tiklash mumkin.");
            }

            $position->restore();

            return redirect()->back()->with('success', 'Lavozim muvaffaqiyatli tiklandi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function getTicket()
    {
        try {
            $dataTickect = [
                "token" => "m4MC0ck4Ku7Ul4L2hHy9Yj3Jx9Xi3IQq6tT7l4Lw"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('GET_TICKET_1C_API_HOST'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataTickect));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', env('1C_API_TOKEN')]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            $result = curl_exec($ch);

            // Asossiyga chiqqanda shuni qoyishim kerak edi
//            $ticket = json_decode($result)->ticket;
            // Asossiyga chiqqanda shuni qoyishim kerak edi

            if ($result === false) {
                throw new Exception("Ticket olishda xatolik: " . curl_error($ch));
            }

            $response1 = json_decode($result, true);

            if (empty($response1['massage']['ticket'])) {
                throw new Exception("1C ticket topilmadi.");
            }

            // Bu O'chib ketadi
            $ticket = $response1['massage']['ticket'];
            // Bu O'chib ketadi
            return $ticket;
        } catch (\Throwable $throwable) {
            return response()->json([
                'status' => false,
                'data' => $throwable->getMessage() . " " . $throwable->getLine()
            ], 200);
        }
    }
}
