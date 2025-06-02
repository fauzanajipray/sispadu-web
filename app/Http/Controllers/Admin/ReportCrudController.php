<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReportRequest;
use App\Models\Report;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReportCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReportCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
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
        CRUD::setModel(\App\Models\Report::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/report');
        CRUD::setEntityNameStrings('laporan', 'laporan');
        // $role = backpack_auth()->user()->role;
        // if(!in_array($role, ['superadmin']))
        // {
        // $this->crud->denyAccess(['delete', 'create', 'show', 'list', 'update']);
        // $this->crud->denyAccess(['create', 'update', 'delete', 'list']);
        // $this->crud->allowAccess(['list', 'show']);
        // }
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name' => 'user_id',
            'label' => 'user',
            'type' => 'relationship',
            'entity'    => 'user', // the method that defines the relationship in your Model
            'attribute' => 'name',
        ]);
        CRUD::column('user_id');
        CRUD::column('content');
        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Status',
            'allows_null' => false,
            'value' => function ($entry) {
                return strtoupper($entry->status);
            },
            'wrapper' => [
                'element' => 'span',
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status) {
                        case Report::SUCCESS:
                            return 'badge badge-info';
                        case Report::REJECTED:
                            return 'badge badge-danger';
                        case Report::PENDING:
                            return 'badge badge-warning';
                        case Report::CANCELLED:
                            return 'badge badge-secondary';
                        default:
                            return 'badge badge-dark';
                    }
                },
            ],
        ]);
        CRUD::column('created_at');
        CRUD::column('updated_at');

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
        CRUD::setValidation(ReportRequest::class);

        CRUD::field('user_id');
        CRUD::field('content');
        CRUD::field('status');

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

    public function show($id)
    {
        $this->crud->hasAccessOrFail('show');


        $this->data['entry'] = $this->crud->getEntry($id);
        // $this->data['entry'] = Transaction::with('transactionPayments')->findOrFail($id);
        $this->data['crud'] = $this->crud;
        // $this->data['products'] = TransactionProduct::where('transaction_id', $id)->get();
        $this->data['products'] = [];
        return view('reports.show', $this->data);
    }

}
