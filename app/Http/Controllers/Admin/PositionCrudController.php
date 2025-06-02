<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PositionRequest;
use App\Models\Position;
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
        CRUD::setEntityNameStrings('Position', 'position');
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
            'label' => 'Parent',
            'type' => 'name',
            'value' => function ($entry) {
                // return $entry->parent ? $entry->parent->name : '-';
                return $entry->parent ? $entry->parent->name . ' (' . $this->getParentHierarchy($entry) . ')' : '-';
            },
        ]);
        $this->crud->addColumn([
            'label' => 'Assigned Users',
            'type' => 'relationship',
            'name' => 'assign',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => 'App\Models\User',
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('user/'.$related_key.'/show');
                },
            ],
        ]);
        // $this->crud->addColumn([
        //     'name' => 'assign',
        //     'label' => 'Assigned Users',
        //     'type' => 'relationship',
        //     'attribute' => 'name',
        //     'model' => \App\Models\User::class,
        //     'pivot' => false, // if you want to show the pivot table data
        //     'wrapper' => [
        //         'href' => function ($crud, $column, $entry, $related_key) {
        //             return backpack_url('user/'.$related_key.'/show');
        //         },
        //     ],
        // ]);
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
            'placeholder' => 'Select a parent position',
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
            'label' => 'Parent',
            'type' => 'name',
            'value' => function ($entry) {
                // return $entry->parent ? $entry->parent->name : '-';
                return $entry->parent ? $entry->parent->name . ' (' . $this->getParentHierarchy($entry) . ')' : '-';
            },
        ]);
        $this->crud->addColumn([
            'name' => 'assign',
            'label' => 'Assigned Users',
            'type' => 'relationship',
            'attribute' => 'name',
            'model' => \App\Models\User::class,
            'pivot' => false, // if you want to show the pivot table data
            'entity' => 'user', // the relationship method in the Position model
        ]);
        CRUD::column('created_at');
        CRUD::column( 'updated_at');
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
