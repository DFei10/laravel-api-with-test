<?php

namespace App;

use App\Models\Announcement;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementFilter
{
    protected Builder $query;

    protected function search(string $value)
    {
        $this->query->whereAny(['title', 'description'], 'like', "%{$value}%");
    }

    protected function type(string $value)
    {
        $type = match ($value) {
            'online' => Announcement::TYPE_ONLINE,
            'offline' => Announcement::TYPE_OFFLINE,
            default => null,
        };

        if ($type !== null) {
        }
        $this->query->where('type', $type === null ? 3 : $type);
    }

    public function apply(Builder $query)
    {
        $this->query = $query;

        $filters = ['search', 'type'];

        foreach (request()->only($filters) as $filter => $value) {
            if (! method_exists($this, $filter)) {
                throw new Exception("filter {$filter} does not exists AnnouncementFilter::class");
            }
            $this->$filter($value);
        }
    }
}
