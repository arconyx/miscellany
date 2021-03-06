<?php

namespace App\Models;

use App\Traits\CampaignTrait;
use App\Traits\ExportableTrait;
use App\Traits\VisibleTrait;
use Kalnoy\Nestedset\NodeTrait;

class Tag extends MiscModel
{
    /**
     * Searchable fields
     * @var array
     */
    protected $searchableColumns  = ['name', 'type', 'entry'];

    /**
     * Entity type
     * @var string
     */
    protected $entityType = 'tag';

    protected $explicitFilters = ['tag_id'];

    /**
     * Fields that can be filtered on
     * @var array
     */
    protected $filterableColumns = [
        'name',
        'type',
        'tag_id',
        'is_private',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'image',
        'entry',
        'tag_id',
        'campaign_id',
        'is_private',
    ];

    /**
     * Nullable values (foreign keys)
     * @var array
     */
    public $nullableForeignKeys = [
        'tag_id',
    ];

    /**
     * Traits
     */
    use CampaignTrait;
    use VisibleTrait;
    use NodeTrait;
    use ExportableTrait;

    /**
     * Performance with for datagrids
     * @param $query
     * @return mixed
     */
    public function scopePreparedWith($query)
    {
        return $query->with(['entity', 'tag', 'tag.entity']);
    }

    /**
     * Parent
     */
    public function tag()
    {
        return $this->belongsTo('App\Models\Tag', 'tag_id', 'id');
    }

    /**
     * Children
     */
    public function tags()
    {
        return $this->hasMany('App\Models\Tag', 'tag_id', 'id');
    }

    /**
     * @return string
     */
    public function getParentIdName()
    {
        return 'tag_id';
    }

    /**
     * Specify parent id attribute mutator
     * @param $value
     */
    public function setTagIdAttribute($value)
    {
        $this->setParentIdAttribute($value);
    }

    /**
     * Detach children when moving this entity from one campaign to another
     */
    public function detach()
    {
        foreach ($this->allChildren(true)->get() as $child) {
            $child->tags()->detach($this->id);
//            if (!empty($child->child)) {
//                $child->child->tag_id = null;
//                $child->child->save();
//            }
        }
        return parent::detach();
    }

    /**
     * Get all the children
     * @return array
     */
    public function allChildren($withTags = false)
    {
        $children = [];
        foreach ($this->entities()->pluck('entities.id')->toArray() as $entity) {
            $children[] = $entity;
        }
        foreach ($this->descendants as $desc) {
            foreach ($desc->entities()->pluck('entities.id')->toArray() as $entity) {
                $children[] = $entity;
            }
        }

        if ($withTags) {
            return Entity::whereIn('id', $children);
        }
        return Entity::whereIn('id', $children)->whereNotIn('type', ['tag']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function entities()
    {
        return $this->hasManyThrough(
            'App\Models\Entity',
            'App\Models\EntityTag',
            'tag_id',
            'id',
            'id',
            'entity_id'
        );
    }

    /**
     * @return array
     */
    public function menuItems($items = [])
    {
        $campaign = $this->campaign;

        $count = $this->descendants()->acl()->count();
        if ($count > 0) {
            $items['tags'] = [
                'name' => 'tags.show.tabs.tags',
                'route' => 'tags.tags',
                'count' => $count
            ];
        }
        $count = $this->allChildren()->acl()->count();
        if ($campaign->enabled('characters')) {
            $items['children'] = [
                'name' => 'tags.show.tabs.children',
                'route' => 'tags.children',
                'count' => $count
            ];
        }
        return parent::menuItems($items);
    }

    /**
     * Get the entity_type id from the entity_types table
     * @return int
     */
    public function entityTypeId(): int
    {
        return (int) config('entities.ids.tag');
    }
}
