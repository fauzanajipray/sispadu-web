<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
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
        $role = backpack_auth()->user()->role;
        if ($role === 'superadmin') {
            $this->crud->allowAccess(['list', 'create', 'update', 'delete', 'show']);
        } else {
            $this->crud->denyAccess(['create', 'update', 'delete', 'show', 'list']);
            $this->crud->allowAccess(['show']);
        }
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings(__('base.user'), __('users'));
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.
        CRUD::column('name');
        CRUD::column('email');
        CRUD::addColumn([
            'name' => 'role',
            'type' => 'text',
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->role) {
                        case 'superadmin':
                            return 'badge bg-primary';
                        case 'user':
                            return 'badge bg-success';
                    }
                },
            ],
            'label' => __('base.role'),
            'value' => function ($entry) {
                return $entry->role === 'superadmin' ? 'Super Admin' : 'User';
            },
        ]);
        CRUD::addColumn([
            'name' => 'position_id',
            'label' => 'Posisi',
            'allows_null' => true,
            'type' => 'text',
            'value' => fn($entry) => $entry->position ? $entry->position->name . ($this->getParentHierarchy($entry->position) ? ' (' . $this->getParentHierarchy($entry->position) . ')' : '') : '-',
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('position/' . $related_key . '/show');
                },
            ],
        ]);

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */

        $this->crud->addButtonFromModelFunction('line', 'update_jabatan', 'showUpdatePositionButton', 'beginning');
    }


    public function index()
    {
        $this->crud->hasAccessOrFail('list');
        $this->setupListOperation();
        $this->crud->tabsEnabled();

        $this->data['crud'] = $this->crud;

        return view('users.list', $this->data);
    }

    public function getData(Request $request, $id)
    {
        $this->crud->hasAccessOrFail('list');
        $this->setupListOperation();

        // Ambil user dengan relasi position
        $user = User::with('position')->findOrFail($id);

        // Cek apakah posisi tersedia
        if ($user->position) {
            $parentHierarchy = $this->getParentHierarchy($user->position);
            $item = $user->position;
            $user->position_name = $item->name . ($parentHierarchy ? ' (' . $parentHierarchy . ')' : '');
        } else {
            $user->position_name = null;
        }

        return response()->json($user);
    }

    private function getParentHierarchy($position)
    {
        $hierarchy = [];
        $current = $position->parent;

        while ($current) {
            $hierarchy[] = $current->name;
            $current = $current->parent;
        }

        return $hierarchy ? implode(', ', $hierarchy) : '';
    }


    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
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

    public function show(Request $request, $id)
    {
        $this->crud->hasAccessOrFail('show');
        $this->setupListOperation();
        $this->crud->tabsEnabled();

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;

        return view('users.show', $this->data);
    }

    public function updatePosition(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'position_id' => 'nullable|exists:positions,id',
        ]);

        try {
            // Ambil user berdasarkan ID
            $user = User::findOrFail($request->input('user_id'));

            // Perbarui posisi user
            $user->update([
                'position_id' => $request->input('position_id'),
            ]);

            // Kembalikan respons JSON sukses
            return response()->json([
                'success' => true,
                'message' => __('message.user_position_updated'),
            ]);
        } catch (\Exception $e) {
            // Kembalikan respons JSON error
            return response()->json([
                'success' => false,
                'message' => __('message.user_position_update_failed'),
            ], 500);
        }
    }
}
