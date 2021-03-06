<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventory;
use App\Models\Entity;
use App\Models\Inventory;
use App\Models\MiscModel;
use App\Traits\GuestAuthTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Guest Auth Trait
     */
    use GuestAuthTrait;

    /**
     * @var
     */
    protected $transKey;

    /**
     * @var
     */
    protected $viewPath;

    /**
     * @param Entity $entity
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Entity $entity)
    {
        // Policies will always fail if they can't resolve the user.
        if (Auth::check()) {
            $this->authorize('view', $entity->child);
        } else {
            $this->authorizeEntityForGuest('read', $entity->child);
        }

        $ajax = request()->ajax();
        $inventory = $entity
            ->inventories()
            ->select('inventories.*')
            ->with('item')
            ->leftJoin('items as i', 'i.id', '=', 'inventories.item_id')
            ->acl()
            ->orderBy('position', 'ASC')
            ->orderBy('i.name', 'ASC')
            ->paginate();

        return view('entities.pages.inventory.index', compact(
            'ajax',
            'entity',
            'inventory'
        ));
    }

    /**
     * @param Entity $entity
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Entity $entity)
    {
        $this->authorize('update', $entity->child);

        $ajax = request()->ajax();

        return view('entities.pages.inventory.create', compact(
            'entity',
            'ajax'
        ));
    }

    /**
     * @param Request $request
     * @param Model $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreInventory $request, Entity $entity)
    {
        $this->authorize('update', $entity->child);

        $data = $request->only(['amount', 'item_id', 'entity_id', 'position', 'description', 'visibility']);
        $ajax = $request->ajax();

        $inventory = new Inventory();
        $inventory = $inventory->create($data);

        return redirect()
            ->route('entities.inventory', $entity)
            ->with('success', trans('entities/inventories.create.success', [
                'item' => $inventory->item->name,
                'entity' => $entity->name
            ]));
    }

    /**
     * @param Entity $entity
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Entity $entity, Inventory $inventory)
    {
        $this->authorize('update', $entity->child);

        $ajax = request()->ajax();

        return view('entities.pages.inventory.update', compact(
            'entity',
            'inventory',
            'ajax'
        ));
    }

    /**
     * @param Request $request
     * @param Model $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreInventory $request, Entity $entity, Inventory $inventory)
    {
        $this->authorize('update', $entity->child);

        $data = $request->only(['amount', 'item_id', 'entity_id', 'position', 'description', 'visibility']);
        $ajax = $request->ajax();

        $inventory->update($data);
        $inventory->refresh();

        return redirect()
            ->route('entities.inventory', $entity)
            ->with('success', trans('entities/inventories' . '.update.success', [
                'item' => $inventory->item->name,
                'entity' => $entity->name
            ]));
    }

    /**
     * @param Model $model
     * @param Model $relation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Entity $entity, Inventory $inventory)
    {
        $this->authorize('update', $entity->child);

        $inventory->delete();

        return redirect()
            ->route('entities.inventory', [$entity->id])
            ->with('success', trans('entities/inventories.destroy.success', [
                'item' => $inventory->item->name,
                'entity' => $entity->name
            ]));
    }
}
