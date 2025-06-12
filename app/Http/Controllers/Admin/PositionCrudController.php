<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PositionRequest;
use App\Models\Position;
use App\Models\Report;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class PositionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PositionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Position::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/position');
        CRUD::setEntityNameStrings(__('position'), __('positions'));
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('detail');
        $this->crud->addColumn([
            'name' => 'parent_id',
            'label' => __('base.parent'),
            'type' => 'name',
            'value' => function ($entry) {
                // return $entry->parent ? $entry->parent->name : '-';
                return $entry->parent ? $entry->parent->name . ' (' . $this->getParentHierarchy($entry) . ')' : '-';
            },
        ]);
        CRUD::addColumn([
            'name' => 'assign',
            'label' => __('base.assigned_user'),
            'allows_null' => true,
            'type' => 'text',
            'value' => fn($entry) => $entry->user ? $entry->user->name : '-',
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return $entry->user ? backpack_url('user/' . $entry->user->id . '/show') : '';
                },
            ],
        ]);
        CRUD::column('created_at');
        CRUD::column('updated_at');

        $this->crud->addButtonFromModelFunction('top', 'structure_hierarchy_button', 'structure_hierarchy_button', 'end');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PositionRequest::class);

        CRUD::field('name');
        CRUD::field('detail');
        // CRUD::field('parent_id');
        $this->crud->addField([
            'name' => 'parent_id',
            'label' => 'Parent Position',
            'type' => 'select2_from_ajax',
            'attribute' => 'name',
            'include_all_form_fields' => true,
            'entity' => 'parent',
            'method' => 'POST',
            'delay' => 500,
            'data_source' => url('webapi/position/list-parent'),
            'placeholder' => __('select_parent_position'),
        ]);


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        CRUD::column('name');
        CRUD::column('detail');
        $this->crud->addColumn([
            'name' => 'parent_id',
            'label' => __('base.parent'),
            'type' => 'name',
            'value' => function ($entry) {
                // return $entry->parent ? $entry->parent->name : '-';
                return $entry->parent ? $entry->parent->name . ' (' . $this->getParentHierarchy($entry) . ')' : '-';
            },
        ]);
        CRUD::addColumn([
            'name' => 'assign',
            'label' => __('base.assigned_user'),
            'allows_null' => true,
            'type' => 'text',
            'value' => fn($entry) => $entry->user ? $entry->user->name : '-',
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return $entry->user ? backpack_url('user/' . $entry->user->id . '/show') : '';
                },
            ],
        ]);
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    public function show(Request $request, $id)
    {
        $this->crud->hasAccessOrFail('show');
        $this->setupListOperation();
        $this->crud->tabsEnabled();

        $entry = $this->crud->getEntry($id);
        $this->data['entry'] = $entry;
        $this->data['crud'] = $this->crud;
        $this->data['reports'] = Report::whereHas('dispositions', function ($query) use ($entry) {
            $query->where('to_position_id', $entry->id);
        })->with('user')->get();
        
        return view('positions.show', $this->data);
    }

    public function showHierarchy()
    {
        // Ambil semua posisi yang tidak punya parent (root)
        $positions = Position::whereNull('parent_id')->with('childrenRecursive')->get();

        return view('positions.structure', compact('positions'));
    }


    public function listParentPositions(Request $request)
    {
        $term = $request->input('q');
        if ($term) {
            $data = Position::where('name', 'like', '%' . $term . '%')
                ->orWhere('detail', 'LIKE', '%' . $term . '%')
                ->where('id', '!=', 1)
                ->paginate(10);
        } else {
            $data = Position::paginate(10);
        }

        $data->getCollection()->transform(function ($item) {
            $parentHierarchy = $this->getParentHierarchy($item);
            return [
                'id' => $item->id,
                'name' => $item->name . ($parentHierarchy ? ' (' . $parentHierarchy . ')' : ''),
            ];
        });

        return $data;
    }

    public function listPositionsWithoutUser(Request $request, $id)
    {
        // $id is user_id
        // make Position whereDoesntHave('user') and include data where relation to user with $id

        $term = $request->input('q');

        $query = Position::whereDoesntHave('user', function ($q) use ($id) {
            $q->where('id', '!=', $id);
        })->orWhereHas('user', callback: function ($q) use ($id) {
            $q->where('id', $id);
        });

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                    ->orWhere('detail', 'LIKE', '%' . $term . '%');
            });
        }
        $data = $query->paginate(10);
        $data->getCollection()->transform(function ($item) {
            $parentHierarchy = $this->getParentHierarchy($item);
            return [
                'id' => $item->id,
                'text' => $item->name . ($parentHierarchy ? ' (' . $parentHierarchy . ')' : ''),
            ];
        });
        return $data;
    }

    public function listPositions(Request $request)
    {
        $term = $request->input('q');
        if ($term) {
            $data = Position::where('name', 'like', '%' . $term . '%')
                ->orWhere('detail', 'LIKE', '%' . $term . '%')
                ->paginate(10);
        } else {
            $data = Position::paginate(10);
        }

        $data->getCollection()->transform(function ($item) {
            $parentHierarchy = $this->getParentHierarchy($item);
            return [
                'id' => $item->id,
                'text' => $item->name . ($parentHierarchy ? ' (' . $parentHierarchy . ')' : ''),
            ];
        });

        return $data;
    }

    private function getParentHierarchy($position)
    {
        $hierarchy = [];
        $current = $position->parent;

        while ($current) {
            $hierarchy[] = $current->name;
            $current = $current->parent;
        }

        return $hierarchy ? '' . implode(', ', $hierarchy) : '';
    }
}
