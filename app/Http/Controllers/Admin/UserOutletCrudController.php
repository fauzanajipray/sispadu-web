<?php

namespace App\Http\Controllers\Admin;

use App\Models\Outlet;
use App\Models\UserOutlet;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;
use App\Http\Requests\UserOutletRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserOutletCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserOutletCrudController extends CrudController
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
        CRUD::setModel(\App\Models\UserOutlet::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user-outlet');
        CRUD::setEntityNameStrings('user outlet', 'user outlets');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn(
            [
                'name'      => 'outlet_id',
                'label'     => 'Outlet',
                'type'        => 'select_from_array',
                'options'    => Outlet::pluck('name','id'),
                'attributes' => [
                    'class' => 'form-control py-1'
                ]
            ],
        );
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('phone');
        CRUD::addColumn(['name' => 'status', 'label' => 'Status', 'type' => 'text']); 

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
        CRUD::setValidation(UserOutletRequest::class);

        CRUD::addField(
            [
                'name'      => 'outlet_id',
                'label'     => 'Outlet',
                'type'       => 'select_from_array',
                'options'    => Outlet::pluck('name','id'),
                'attributes' => [
                    'class' => 'form-control py-1'
                ]
            ],
        );
        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('phone');
        CRUD::field('password')->type('password');
        CRUD::field('is_active')->label('Active');

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

    public function update(Request $request, $id)
    {
        $user = UserOutlet::findOrFail($id);
        $user->name = $request->input('outlet_id');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->is_active = $request->input('is_active');
        if($request->input('password'))
        {
            $user->password = $request->input('password');
        }
        else
        {
            $user->password = $user->password;
        }
        $user->save();
        Alert::success(trans('backpack::crud.update_success'))->flash();
        return redirect()->to($this->crud->route);
    }
}
