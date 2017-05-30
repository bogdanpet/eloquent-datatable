## Description
PHP class for displaying Eloquent collections as HTML table. Tested in Laravel, but should work in any project using eloquent as standalone package.
## Installation
Download and require Datatable.php and DatatableActions.php.

Composer installation comming soon.
## Usage
#### Basic usage
For example, let's say that we have collection of users we want to show as a table. Creating collection and sharing it to your view should look like this in Laravel.
```
$users = User::all();

return view('users', compact('users'));
```
Now, in your view file, create new instance of Datatable class, and set main data and columns to be displayed in a table.
```
<?php 
$datatable = new \Bogdanpet\Datatables\Datatable();
$datatable->setData($users);
$datatable->setColumns(['id, 'name', 'email']);
?>
```
Then just put table where you want it using show() method.
```
<div class="table-container">
    {!! $datatable->show(); !!}
</div>
```
If you need to customize table more, you can use separate methods instead of show() method.
```
<div class="table-container">
    {!! $datatable->open(['class' => 'table-responsive']); // Generate opening <table> tag with optional attributes. !!}
    {!! $datatable->tableHead(); // Generate table column headings. !!}
    {!! $datatable->tableBody(); // Generate main table rows. !!}
    {!! $datatable->tableFoot(); // Generate table footer. !!}
    {!! $datatable->close(); // Generate closing </table> tag. !!}
    {!! $datatable->pagination(); // Generate pagination links if you are using User::paginate() to create collection !!}
</div>
```
#### Static usage
Instead of creating new instance and setting data and columns, you can create table directly with static method make().
```
<div class="table-container">
    {!! \Bogdanpet\Datatables\Datatable::make($users, ['id, 'name', 'email'] !!}
</div>
```
#### Datatable actions
For generating action links I provided custom column 'actions'. For example, if you want to create links to edit and delete user from database, you can add 'actions' to columns and then set actions.
```
$datatable->setColumns(['id, 'name', 'email', 'actions']);
$datatable->setActions([
    [ 'Edit', '/user/edit/{id}', ['class' => 'table-action'] ],
    [ 'Delete', '/user/delete/{id}', ['class' => 'table-action'] ]
]);
```
As you can see, actions are set as array of arrays, each array contains link text as first item, link href as second and optional third item which is array of link attributes.
{id} is wildcard similar like one in laravel routing system, and will be replaced with corresponding user's id from the database. You can use wildcard for any database column like {name} or {email}.
Example above will generate two links for each user in actions column.
```
<a href="/user/edit/23" class="table-action">Edit</a>
<a href="/user/delete/23" class="table-action">Delete</a>
```
#### Advanced usage
For complete control over your datatable you can extend main Datatable class. For example, Let's create UsersDatatable class.
```
<?php namespace App\Datatables;

class UsersDatatable extends \Bogdanpet\Datatables\Datatable
{
    // Add this line if you want to use actions.
    use \Bogdanpet\Datatables\DatatableActions;

    // Set columns
    protected $columns = [
        'row_num',
        'name',
        'email',
        'actions'
    ];
    
    // Set actions
    protected $actions = [
        [ 'Edit', '/user/edit/{id}', ['class' => 'table-action'] ],
        [ 'Delete', '/user/delete/{id}', ['class' => 'table-action'] ]
    ]
}
```
After class is created, it can be used same as main class. Just no need for defining columns and actions. Main data can also be defined, but it is not recommended because of its dynamic behavior.
```
<?php
$users_datatable = new \App\Datatables\UsersDatatable();
$users_datatable->setData($users);
?>
```
```
<div class="table-container">
    {!! $users_datatable->show(); !!}
</div>
```
'row_num', like 'actions', is predefined custom column which generate number of row in a table. You can also add your own custom columns or customize existing ones using 'th' and 'td' methods. Let's add new custom column 'registered_at' and customize 'name' column in our UsersDatatable class above.
```
<?php namespace App\Datatables;

class UsersDatatable extends \Bogdanpet\Datatables\Datatable
{
    // Add this line if you want to use actions.
    use \Bogdanpet\Datatables\DatatableActions;

    // Set columns
    protected $columns = [
        'row_num',
        'name',
        'email',
        'registered_at',
        'actions'
    ];
    
    // Set actions
    protected $actions = [
        [ 'Edit', '/user/edit/{id}', ['class' => 'table-action'] ],
        [ 'Delete', '/user/delete/{id}', ['class' => 'table-action'] ]
    ]
    
    // Column headings.
    // Create method which name starts wit th and contains name of a column in studly case.
    
    public function thName()
    {
        return $this->th('Name', 'table-heading-large'); // th() generate <th> element, parameters are content, and optional classes.
    }
    
    public function thRegisteredAt()
    {
        return $this->th('Registered', 'table-heading-small'); // th() generate <th> element, parameters are content, and optional classes.
    }
    
    // Column cells
    // Create method which name starts wit td and contains name of a column in studly case. td methods accepting eloquent model as parameter.
    
    public function tdName($model)
    {
        // Customize 'name' column to display names with capitalized letters.
        return $this->td( ucwords($model->name), 'table-cell-large'); // td() generate <td> element, parameters are content, and optional classes.
    }
    
    public function tdRegisteredAt($model)
    {
        // 'registered_at' now displays user's creation date and time in human readable format.
        return $this->td( $model->created_at->toDayDateTimeString(), 'table-cell-small');
    }
    
}
```
