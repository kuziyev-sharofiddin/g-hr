<?php

namespace App\Http\Resources;

use App\Enums\FeatureBookStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $authUser = auth()->user();
        $workerId = $authUser ? $authUser->worker_id : null;

        $featuredBooks = $this->featuredBooks;
        $likedBooks = $featuredBooks->where('status', FeatureBookStatus::LIKED)->whereNotNull('worker_id');
        $ratedBooks = $featuredBooks->where('status', FeatureBookStatus::RATING)->whereNotNull('worker_id');
        $savedBooks = $featuredBooks->where('status', FeatureBookStatus::BOOKMARK);
        $unreadBooks = $featuredBooks->where('status', FeatureBookStatus::FUTURE_READ);
        $finishReadBooks = $featuredBooks->where('status', FeatureBookStatus::FINISH_READ);

        return [
            'id' => $this->id,
            "book_details" => $this->booksLanguages ? $this->booksLanguages->map(function ($booksLanguage) {
                return [
                    'language_id' => $booksLanguage->book_language_id ?? null,
                    'language_name' => $booksLanguage->language->name ?? null,
                    'name' => $booksLanguage->name ?? null,
                    'short_description' => $booksLanguage->short_description ?? null,
                    'long_description' => $booksLanguage->long_description ?? null,
                ];
            }) : [],

            'book_author' => [
                'id' => $this->bookAuthor->id ?? null,
                'name' => $this->bookAuthor->name ?? null,
            ],
            'book_language' => $this->booksLanguages ? $this->booksLanguages->map(function ($booksLanguage) {
                return [
                    'id' => $booksLanguage->language->id ?? null,
                    'name' => $booksLanguage->language->name ?? null,
                ];
            }) : [],
            'book_genre' => [
                'id' => $this->bookGenre->id ?? null,
                'name' => $this->bookGenre->name ?? null,
                'name_ru' => $this->bookGenre->name_ru ?? null,
            ],
            'book_status' => $this->book_status,
            'status' => $this->status ?? null,
            'responsible_worker' => $this->responsible_worker,
            'responsible_worker_id' => $this->responsible_worker_id,
            'recommended_by_worker' => $this->recommended_by_worker,
            'recommended_by_worker_id' => $this->recommended_by_worker_id,
            'image_path' => $this->image_path,
            'likes' => $likedBooks->count(),
            'comments' => $this->comments->count(),
            'ratings' => $ratedBooks->count(),
            'finish_read_book_language' => $finishReadBooks->isNotEmpty()
                ? BookLanguageResource::collection(
                    $finishReadBooks->load('featureBookLanguages')->pluck('featureBookLanguages')->flatten()->unique('id')
                )
                : null,
            'read_workers' => $finishReadBooks->count(),
            'average_rating' => round($ratedBooks->avg('rating'), 1),
            'is_like' => $workerId ? $likedBooks->pluck('worker_id')->contains($workerId) : false,
            'is_saved' => $workerId ? $savedBooks->pluck('worker_id')->contains($workerId) : false,
            'is_unread' => $workerId ? $unreadBooks->pluck('worker_id')->contains($workerId) : false,
            'is_read' => $workerId ? $finishReadBooks->pluck('worker_id')->contains($workerId) : false,
            'created_at' => $this->created_at->format('d.m.Y'),
        ];
    }
}
