# LivewireSelect2

Package to integrate the select2 library with livewire, designed to reuse and encapsulate methods, and above all easy to implement in livewire components..

## Requirements

- **php8.2**
- **Select2**: Have the select2 library installed [aquí](https://select2.org).
- **Jquery**: Have the Jquery library installed, select2 depends on it. [aquí](https://jquery.com).

## Setup
```bash
  composer require laspi94/livewire-select2
```

Call the directive package

```html
<body>
    @LivewireSelect2
```

## Configuration

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | You can cinfugre your theme, provider by select2.org
    |
    | can be: classic or bootstrap5
    */

    'theme' => 'bootstrap-5',

    /*
    |--------------------------------------------------------------------------
    | Optional prefix for routes
    |--------------------------------------------------------------------------
    |
    | Assigns some optional prefix for the dynamically generated routes from the 
    | configuration file.
    |
    */

    'prefix' => 'select2',

    /*
    |--------------------------------------------------------------------------
    | Middleware for ajax routes
    |--------------------------------------------------------------------------
    |
    | Assign the middleware through which you need your requests to pass in each call
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Config and publish ajax route
    |--------------------------------------------------------------------------
    |
    | Here you can specify your route settings to make the ajax calls, assigning 
    | the controller, URL patch, name and method to whatever the field calls, via 
    | an alias.
    | 
    |
    */

    'routes' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | You can create your own template to display an error message for validations
    | issued by livewire, the element will be created after the input.
    |
    */

    'error_template' => '<span class="invalid-feedback" role="alert"><strong>:message</strong></span>',
];
```

#### Example route.

```php
    'routes' => [
        'alias' => [
          'uri' => '/list-select2',
          'controller' => \App\Http\Controllers\ExampleController::class,
          'method' => 'listSelect2',
          'name' => 'list.select2'
          'placeholder' => 'Select one',
          'showIdOption' => false,
          'defaultValue' => null
        ];
    ],
```

## Use

Import the LivewireSelect2 trait
```php
    use Livewire\Select2\Traits\LivewireSelect2;
    
    class SomeAwesomeComponent extends Component
    {
        use LivewireSelect2;
```

In the mount method of the component, we will assign the properties that are associated with the field.
```php
    public function mount()
    {
        $this->select2Field([
            'field' => 'alias'
        ]);
    }
```

After the component starts, we will have to call the $this->loadSelect2() method, as we know, select2 needs the DOM already rendered to apply the properties and listeners in the document.

```php
    public function someMehotdAfterInit()
    {
        $this->loadSelect2();
    }
```

Inside our controller we also import our trait to access the select2PaginateList() method
This is the one that will be responsible for returning our paginated items, the method assigned in our route should look something like this.

```php
    use LivewireSelect2;

    public function listSelect2(Request $request)
    {
        try {
            $data = $request->all();

            switch ($data['queryParams']) {
                case 'case1':
                    $extraQuery = function ($query) {
                        $query->someScope()
                            ->whereNotIn('asociate.column', ['someValue'])
                            ->orderBy('another_asociate.column', 'asc');
                    };
                    break;
                default:
                    $extraQuery = function ($query) {
                        $query->someScopeByDefault();
                    };
                    break;
            }

            return $this->select2PaginateList(Model::class, $data['q'], $extraQuery, $data['paginate'], $data['initialValue']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'error' => $th->getMessage()], 500);
        }
    }
```

After that we must indicate in our model the columns that will be consulted in each call to AJAX, after adding the main columns, you can add more columns for which you want it to do the corresponding search within your model.
```php
  public $select2filter = [
       'id' => 'table.id',
       'text' => 'table.description',
       'filter' => 'table.another_column_filter_on_serch',
       'filter2' => 'table.another_column_filter_on_serch',
  ];
```

If you need to load a select2 that depends on another field, you can do it in the following way, passing the necessary parameter for the query and establishing the default value of our main field before calling "loadSelect2" again.
```php
    $someModel = Model::find($this->ubicacion_cliente);

    if ($someModel) {
      $this->select2Field['someField']['queryParams']['anotherSelect2FieldValue'] = $this->anotherSomeField;

      $this->select2Field['someField']['defaultValue'] = [
        'id' => $someModel->id,
        'text' => $someModel->description
      ];
    } else {
      $this->select2Field['someField']['defaultValue'] = null;
    }

    $this->loadSelect2();
```

Now, with this same alias and just by manipulating the query you can easily reuse the query you want to perform, obtaining the paginated, preselected results without overloading the DOM.

### HTML EXAMPLE

```html
  <div wire:ignore>
    <label for="someField">Ubicación cliente</label>
      <select class="select2" id="someField" name="someField">
      </select>
  </div>
```

## Using by

Este proyecto es utilizado por las siguientes empresas:

- IDL S.A.

## License

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)

## Authors

- [@laspi94](https://www.github.com/laspi94)
