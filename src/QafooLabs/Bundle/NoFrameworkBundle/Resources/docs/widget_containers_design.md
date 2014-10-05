# Widget Containers

Symfony lacks a component based HMVC approach, which decouples components from
each other fully. The widget support in `QafooLabsFrameworkExtraBundle` adds
this support based on a model developed by Facebook and improved by
ResearchGate.

The central concept of this approach is a widget, which yields requirements
for the data it needs to render itself. Their properties are:

- Widgets can be composed of several child widgets
- Every widget has its own url
- A widget contains ALL view related code: Widget class, template, javascript and css file.
- Child widgets can optionally be rendered asynchroneously or through ESI

The preparer collects the data requirements of all widgets until all the
requirements are resolved and then instructs the widgets to render themselves.

Every widget has to implement a base class, an example of a simple widget
with a service lookup for data and rendering of a child-widget looks like this:

```php
class BlogPostWidget extends Widget
{
    protected $post;
    protected $comments;

    public function collect(Request $request)
    {
        yield array(
            new ServiceRequirement('post', 'service_name', 'method', [$request->query->get('id')]),
            new WidgetRequirement('comments', 'blog_post_comments'),
        );
    }
}
```

The requirement preparer knows about the services and calls them with the given arguments.
When the preparer collects and resolves all the requirements it builds a list of all
the widgets that are part of the current request, and then resolving them recursively,
adding newly found widget requirements to the list.

A number of different requirements is already available with this bundle.

### ServiceRequirement

A service requirement is an invocation of a service registered with the preparer.

The following arguments are passed to a `ServiceRequirement`:

- `$variable` contains the name of the variable on the widget to put the result in.
- `$serviceName` is the name of the service which is invoked by the preparer
- `$serviceMethod` is the name of the method invoked on the method
- `$arguments` contains the values passed to the method invocation using `call_user_func_array`

A service can be any php object. The result of a service requirement can not be
cached accross different widgets though, which makes them inefficient when
called several times during a single request.

### EntityRequirement

The entity requirement allows to multi-fetch data objects by their ID and cache the return value.

The following arguments are passed to an `EntityRequirement`:

- `$variable` contains the name of the variable on the widget to put the result in.
- `$className` is the entity class name
- `$id` is a scalar or array of scalars of entity ids

### WidgetRequirement

The widget requirement registers a child widget.

- `$variable` contains the name of the variable on the widget to render the HTML response into.
- `$widget` name of the widget to render
- `$attributes` is an optional array of request attributes
- `$variables` is an optional array of data dependencies to prefill on a widget dependency

### SubRequestRequirement

When you still use Symfony sub-requests and have some actions you want to render
then the sub request requirement is for you.

- `$variable` contains the name of the variable on the widget to render the HTML response into.

TODO: More details
