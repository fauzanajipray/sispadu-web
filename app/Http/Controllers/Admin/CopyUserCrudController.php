<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Prologue\Alerts\Facades\Alert;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CopyUserCrudController extends CrudController
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
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');

        $role = backpack_auth()->user()->role;
        if(!in_array($role, ['superadmin']))
        {
            $this->crud->denyAccess(['list','update', 'create', 'delete']);
        }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // $this->crud->addClause('where', 'role', 'superadmin');

        CRUD::column('name');
        CRUD::column('email');

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
        CRUD::setValidation(UserRequest::class);

        CRUD::field('name');
        CRUD::field('email');
        CRUD::field('password')->type('password');
        CRUD::addField(
            [
                'name'  => 'role',
                'type'  => 'hidden',
                'value' => 'superadmin',
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

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
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

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Perform your custom validation here
        if ($user->id === backpack_auth()->user()->id) {
            $arr['danger'] = ["Failed to delete data. You cannot delete your own account."];

            return $arr;
        }

        // If validation passes, proceed with deletion
        $user->delete();
        Alert::success(trans('User successfully deleted.'))->flash();
        return true;
    }

    public function show(Request $request, $id)
    {
        $this->crud->hasAccessOrFail('show');
        $this->setupListOperation();

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        // $this->data['stocks'] = Stock::join('products', 'products.id', '=', 'stocks.product_id')
        //     ->where('branch_id', $id)
        //     ->where('quantity', '>', 0)
        //     ->select('stocks.*', 'products.*')
            // ->get();
        return view('users.show', $this->data);
    }
}
